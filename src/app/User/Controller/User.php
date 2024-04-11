<?php

namespace App\User\Controller;

use App\User\Model\User as UserModel;
use Base\Router\Route;
use Base\Validation\Validate;
use Base\Auth\Auth;
use Base\Session\Session;
use Illuminate\Validation\ValidationException;

class User
{
    protected $validation;
    protected $userModel;
    protected $auth;
    protected $session;

    protected $userRules = [
        'name' => 'required|max:50',
        'email' => 'required|email|max:50',
        'password' => 'required|max:50|confirmed'
    ];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->validation = Validate::getInstance()->getValidation();
        $this->auth = new Auth();
        $this->session = new Session();
    }

    public function createUser($req)
    {
        $user = $req['body'] ?? null;

        try {
            $this->validation->make($user, $this->userRules)->validate();
        } catch (ValidationException $e) {
            return Route::setResponse(Route::BAD_REQUEST, $e->errors());
        }

        $userFound = $this->userModel->findByEmail($user['email']);

        if ($userFound) {
            return Route::setResponse(Route::BAD_REQUEST, null, 'Email already exist!');
        }

        unset($user['password_confirmation']);

        $user['password'] = $this->auth->hashPassword($user['password']);

        $id = $this->userModel->create($user);

        return Route::setResponse(Route::SUCCESS, $id);
    }

    public function getUser($req)
    {
        $user = [];

        $userId = $req['id'] ?? null;
        $search = $req['query']['search'] ?? '';
        $search = urldecode($search);
        $page = $req['query']['page'] ?? null;
        $limit = $req['query']['limit'] ?? null;

        if ($userId) {
            $user = $this->userModel->findById($userId);
        } else {
            $user = $this->userModel->findAll($search, $page, $limit);
        }

        if (!$user) {
            return Route::setResponse(Route::NOT_FOUND, null, 'No records found!');
        }

        $user = [
            'data' => $user,
            'totalCount' => $this->userModel->getTotalRowCount(),
            'limit' => $limit,
            'page'  => $page
        ];

        return Route::setResponse(Route::SUCCESS, $user);
    }

    public function updateUser($req)
    {
        $userId = $req['id'] ?? null;
        $user = $req['body'] ?? null;

        try {
            $this->validation->make($user, $this->userRules)->validate();
        } catch (ValidationException $e) {
            return Route::setResponse(Route::BAD_REQUEST, $e->errors());
        }

        $userFound = $this->userModel->findById($userId);

        if (!$userFound) {
            return Route::setResponse(Route::BAD_REQUEST, null, 'User record not found!');
        }

        $userFoundByEmail = $this->userModel->findByEmail($user['email']);

        if ($userFoundByEmail && $userFoundByEmail['email'] != $userFound['email']) {
            return Route::setResponse(Route::BAD_REQUEST, null, 'Email already exist');
        }

        unset($user['password_confirmation']);

        $user['password'] = $this->auth->hashPassword($user['password']);

        $isUpdated = $this->userModel->update($userId, $user);

        if (!$isUpdated) {
            return Route::setResponse(Route::CONFLICT, null, 'Cannot update user record for the moment');
        }

        return Route::setResponse(Route::SUCCESS);
    }

    public function deleteUser($req)
    {
        $userId = $req['id'] ?? '';

        $isDeleted = $this->userModel->delete($userId);

        if (!$isDeleted) {
            return Route::setResponse(Route::BAD_REQUEST, null, "User not found!");
        }

        return Route::setResponse(Route::SUCCESS);
    }

    public function loginUser($req)
    {
        $user = $req['body'] ?? null;

        try {
            $rules = [
                'email' => $this->userRules['email'],
                'password' => 'required|max:50'
            ];
            $this->validation->make($user, $rules)->validate();
        } catch (ValidationException $e) {
            return Route::setResponse(Route::BAD_REQUEST, $e->errors());
        }

        $userFound = $this->userModel->findByEmail($user['email']);

        if (!$userFound) {
            return Route::setResponse(Route::UNAUTHORIZED, null, 'Invalid email or password');
        }

        if(!$this->auth->verifyPassword($user['password'], $userFound['password'])) {
            return Route::setResponse(Route::UNAUTHORIZED, null, 'Invalid email or password');
        }

        unset($userFound['password']);

        $this->session->set(Session::USER_KEY, $userFound);

        return Route::setResponse(Route::SUCCESS, null, 'User was logged in successfully');
    }

    public function userAccount($req)
    {
        $user = $this->session->get(Session::USER_KEY);
        
        if (!$user) {
            return Route::setResponse(Route::UNAUTHORIZED, '', 'Please login.');
        }

        return Route::setResponse(Route::SUCCESS, $user);
    }

    public function userLogout($req) 
    {
        $this->session->destroy();
        return Route::setResponse(Route::SUCCESS);
    }
}
