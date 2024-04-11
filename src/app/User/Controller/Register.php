<?php

namespace App\User\Controller;

use Base\Views\Render;
use Base\Session\Session;
use Base\Router\Route;

class Register
{
    const VIEWS = "User/view/register";

    protected $request;
    protected $session;
    protected $user;

    public function __construct($request)
    {
        $this->request = $request;
        $this->session = new Session();
        $this->user = $this->session->get(Session::USER_KEY);

        if ($this->user) {
            Route::redirect('/user/account');
        }
    }

    public function create()
    {
        return Render::views(self::VIEWS);
    }
}
