<?php

namespace App\controllers;
use \PDO;
use League\Plates\Engine;
use \Tamtamchik\SimpleFlash\Flash;
use App\QueryBuilder;
use \Delight\Auth\Auth;

class UserController extends HomeController {

    public function login() {

        try {
            $this->auth->login($_POST['email'], $_POST['password']);
            $this->flash->success('Вы успешно авторизовались');
            header("Location: /users");
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->flash->error('Неверный е-мэйл адрес');
            header("Location: /page_login");
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->flash->error('Неверный пароль');
            header("Location: /page_login");
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            $this->flash->error('Е-мэйл не подтвержден');
            header("Location: /page_login");
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->flash->error('Слишком много запросов');
            header("Location: /page_login");
        }
    }

    public function register() {

        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'], null, function ($selector, $token) {
                echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email)';
            });
        
            echo 'We have signed up a new user with the ID ' . $userId;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->flash->error('Такой е-мэйл уже занят');
            header("Location: /page_login");
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->flash->error('Неверный пароль');
            header("Location: /page_login");
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $this->flash->error('Такой пользователь уже существует');
            header("Location: /page_login");
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->flash->error('Слишком много запросов');
            header("Location: /page_login");
        }
    }

    public function logout() {
        $this->auth->logOut();
        header("Location: /page_login");
    }

    public function create_user_handler() {
        $this->checkLogin();
        if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) die('not admin');

        try {
            $userId = $this->auth->admin()->createUser($_POST['email'], $_POST['password'], null);
            $this->qb->insert('users_info', ['user_id' => $userId, 'name' => $_POST['name'], 'phone' => $_POST['phone'], 'job_title' => $_POST['job_title'], 'address' => $_POST['address']]);
            $user = $this->qb->updateUserInfo($userId, $_POST);
            $this->flash->success('Профиль успешно создан. ID: '. $userId);
            header("Location: /users");
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
    }

    public function role() {
        return $this->auth->hasRole(\Delight\Auth\Role::ADMIN);
    }

    public function user_delete($id) {
        $this->checkLogin();
        if (!$this->role()) die('not admin');
        try {
            $this->auth->admin()->deleteUserById($id);
            $this->flash->success('Успешно');
            header("Location: /users");
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown ID');
        }
    }
}

