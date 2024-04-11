<?php

namespace App\User\Controller;

use Base\Views\Render;
use Base\Session\Session;
use Base\Router\Route;

class Account {

    const VIEWS = "User/view/account";

    protected $request;
    protected $session;
    protected $user;

    public function __construct($request)
    {
        $this->request = $request;
        $this->session = new Session();
        $this->user = $this->session->get(Session::USER_KEY);
        
        if (!$this->user) {
            Route::redirect('/user/login');
        }
    }

    public function create() {
        return Render::views(self::VIEWS, ["user" => [1,2,3,4,5]]);
    }
}