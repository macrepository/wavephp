<?php

namespace App\User\Controller;

use Base\Views\Render;
use Base\Session\Session;
use Base\Router\Route;
use App\User\Controller\User as UserApi;

class Account
{
    const VIEWS = "User/view/account";

    protected $request;
    protected $session;
    protected $user;
    protected $userApi;

    public function __construct($request)
    {
        $this->request = $request;
        $this->session = new Session();
        $this->user = $this->session->get(Session::USER_KEY);

        if (!$this->user) {
            Route::redirect('/user/login');
        }

        $this->userApi = new UserApi();
    }

    public function create()
    {
        return Render::views(self::VIEWS, ["user" => $this->user]);
    }

    public function logout()
    {
        $this->userApi->userLogout();
        Route::redirect('/user/login');
    }
}
