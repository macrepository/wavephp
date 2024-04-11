<?php

namespace App\User\Controller;

use Base\Views\Render;
use Base\Session\Session;
use Base\Router\Route;
use Base\Notification\MessageManager;

use App\User\Controller\User as UserApi;

class Login
{
    const VIEWS = "User/view/login";

    protected $request;
    protected $session;
    protected $user;
    protected $messageManager;
    protected $userApi;

    public function __construct($request)
    {
        $this->request = $request;
        $this->session = new Session();
        $this->user = $this->session->get(Session::USER_KEY);
        $this->userApi = new UserApi();

        if ($this->user) {
            Route::redirect('/user/account');
        }

        $this->messageManager = new MessageManager();
    }

    public function create()
    {
        return Render::views(self::VIEWS);
    }

    public function post()
    {
        $result = $this->userApi->loginUser($this->request);
        $result = json_decode($result, true);

        if (!$result) {
            $this->messageManager->setErrorMessage("Error user login");
            return Route::redirect('/user/login');
        }

        if ($result['code'] == Route::SUCCESS) {
            $this->messageManager->setSuccessMessage($result['message']);
            Route::redirect('/user/account');
        }

        $this->messageManager->setErrorMessage($result['message']);
        return Route::redirect('/user/login');
    }
}
