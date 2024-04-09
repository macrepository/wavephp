<?php
namespace Base\Router;

class Route
{
    const REST_API = "/api/";

    protected static $allRoutes = [];
    protected static $method = null;

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
            if (self::match($path, $route['pattern'])) {
                self::invoke(
                    $route['fn'],
                    [
                        ['req' => self::getRequest()],
                        ['res' => self::getResponse()]
                    ]
                );

                return;
            }
        }

        self::setStatus(404);
        echo "Resource page not found.";
    }

    public static function setStatus ($code = 200) {
        http_response_code($code);
    }

    private static function match($path, $pattern)
    {
        if (strpos($pattern, ':') !== false) {
            $pattern = str_replace(':', '([^/]+)', $pattern);
        }
        return preg_match("#^$pattern$#", $path);
    }

    private static function getRequest()
    {
        return [1, 2, 3, 4, 5]; // Dummy data
    }

    private static function getResponse()
    {
        return [5, 4, 3, 2, 1]; // Dummy data
    }

    private static function invoke($fn, $data)
    {
        if (is_callable($fn)) {
            echo call_user_func_array($fn, $data);
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
