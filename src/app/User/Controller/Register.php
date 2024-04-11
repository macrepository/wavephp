<?php

namespace App\User\Controller;

use Base\Views\Render;
use Base\Session\Session;
use Base\Router\Route;
use Base\Notification\MessageManager;

use App\User\Controller\User as UserApi;
use App\User\Model\User as UserModel;

class Register
{
    const VIEWS = "User/view/register";

    protected $request;
    protected $session;
    protected $user;
    protected $messageManager;
    protected $userApi;
    protected $userModel;

    public function __construct($request)
    {
        $this->request = $request;
        $this->session = new Session();
        $this->user = $this->session->get(Session::USER_KEY);
        $this->userApi = new UserApi();
        $this->userModel = new UserModel();

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
        $user = $this->request['body'] ?? null;
        $result = $this->userApi->createUser($this->request);
        $result = json_decode($result, true);

        if (!$result) {
            $this->messageManager->setErrorMessage("Cannot register new user at the moment!");
            return Route::redirect('/user/register');
        }

        if ($result['code'] == Route::SUCCESS) {
            $userFound = $this->userModel->findByEmail($user['email']);
            unset($userFound['password']);
            $this->session->set(Session::USER_KEY, $userFound);
            $this->messageManager->setSuccessMessage($result['message']);
            Route::redirect('/user/account');
        }

        $this->messageManager->setErrorMessage($result['message']);
        return Route::redirect('/user/register');
    }
}
