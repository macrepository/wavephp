<?php

namespace Base\Router;

class Route
{
    const REST_API      = "/api/";
    const SUCCESS       = 'success';
    const BAD_REQUEST   = 'badRequest';
    const UNAUTHORIZED  = 'unauthorized';
    const FORBIDDEN     = 'forbidden';
    const NOT_FOUND     = 'notFound';
    const CONFLICT      = 'conflict';
    const INTERNAL_ERROR = 'internalServerError';

    protected static $allRoutes = [];
    protected static $method = null;
    protected static $request = [];

    public static $httpResponse = [
        self::SUCCESS => [
            "code" => "success",
            "status" => 200,
            "message" => "Request processed successfully."
        ],
        self::BAD_REQUEST => [
            "code" => "bad_request",
            "status" => 400,
            "message" => "Invalid request."
        ],
        self::UNAUTHORIZED => [
            "code" => "unauthorized",
            "status" => 401,
            "message" => "Access denied.",
        ],
        self::FORBIDDEN => [
            "code" => "forbidden",
            "status" => 403,
            "message" => "Forbidden."
        ],
        self::NOT_FOUND => [
            "code" => "not_found",
            "status" => 404,
            "message" => "Resource not found."
        ],
        self::CONFLICT => [
            "code" => "conflict",
            "status" => 409,
            "message" => "Failed to perform the request"
        ],
        self::INTERNAL_ERROR => [
            "code" => "internal_server_error",
            "status" => 500,
            "message" => "Server error. Please try again later."
        ]
    ];

    public static function get($pattern, $fn)
    {
        self::setRoutesInfo('GET', $pattern, $fn);
    }

    public static function post($pattern, $fn)
    {
        self::setRoutesInfo('POST', $pattern, $fn);
    }

    public static function patch($pattern, $fn)
    {
        self::setRoutesInfo('PATCH', $pattern, $fn);
    }

    public static function delete($pattern, $fn)
    {
        self::setRoutesInfo('DELETE', $pattern, $fn);
    }

    public static function isRestApi()
    {
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        return strpos($path, self::REST_API) === 0;
    }

    public static function run()
    {
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $routes = self::$allRoutes[$method] ?? [];

        foreach ($routes as $route) {
            if (self::match($path, $route['pattern'], function ($matches) {
                self::setMatchesData($matches);
            })) {
                self::setReqQuery();
                self::setReqBody();
                self::invoke($route['fn'], [self::getRequest()]);

                return;
            }
        }

        self::setStatus(404);
        echo "Resource page not found.";
    }

    public static function setStatus($code = 200)
    {
        http_response_code($code);
    }

    public static function setResponse($code, $body = null, $message = null)
    {
        $response = Route::$httpResponse[$code];

        // Set Status
        $status = $response['status'];
        self::setStatus($status);

        // Set Response
        unset($response['status']);
        if ($message) {
            $response['message'] = $message;
        }
        $response['body'] = $body;

        return json_encode($response);
    }

    public static function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    private static function setMatchesData($matches)
    {
        $data = array_filter($matches, function ($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);

        self::$request = [...self::$request, ...$data];
    }

    private static function setReqBody()
    {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        $body = [];

        if (strpos($contentType, 'application/json') !== false) {
            $body = json_decode(file_get_contents('php://input'), true);
        } else {
            parse_str(file_get_contents('php://input'), $body);
        }

        if (!$body) {
            $body = $_REQUEST;
        }

        self::$request['body'] = $body;
    }

    private static function setReqQuery()
    {
        $queries = [];
        parse_str($_SERVER['QUERY_STRING'], $queries);
        self::$request['query'] = $queries;
    }

    private static function match(string $urlPath, string $registerPath, $matchesCallback = null): bool
    {
        $urlPath = rtrim(urldecode($urlPath), '/');
        $registerPath = rtrim($registerPath, '/');

        $pattern = preg_replace('/:([^\/]+)/', '(?P<$1>[^/]+)', $registerPath);

        if (preg_match('#^' . $pattern . '$#', $urlPath, $matches)) {

            if (is_callable($matchesCallback)) {
                $matchesCallback($matches);
            }

            return true;
        }

        return false;
    }

    private static function getRequest()
    {
        return self::$request;
    }

    private static function invoke($fn, $data)
    {
        if (is_callable($fn)) {
            try {
                echo call_user_func_array($fn, $data);
            } catch (\Exception $e) {
                echo self::setResponse(self::INTERNAL_ERROR, null, $e->getMessage()) . "<br>";
                echo "File: " . $e->getFile() . "<br>";
                echo "Line: " . $e->getLine() . "<br>";
            } catch (\Throwable $t) {
                echo self::setResponse(self::INTERNAL_ERROR, null, $t->getMessage()) . "<br>";
                echo "File: " . $t->getFile() . "<br>";
                echo "Line: " . $t->getLine() . "<br>";
            }
        } else {
            throw new \Exception("Cannot invoke");
        }
    }

    private static function setRoutesInfo($method, $pattern, $fn)
    {
        self::$allRoutes[$method][] = [
            'pattern' => $pattern,
            'fn' => $fn
        ];
    }
}
