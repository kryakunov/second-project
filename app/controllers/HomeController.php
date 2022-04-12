<?php

namespace App\controllers;
use \PDO;
use League\Plates\Engine;
use \Tamtamchik\SimpleFlash\Flash;
use App\QueryBuilder;
use \Delight\Auth\Auth;

class HomeController {

    protected $templates;
    protected $auth;
    protected $db;
    protected $qb;
    protected $flash;

    public function __construct(Engine $engine, PDO $pdo, Auth $auth) {
        
        $this->templates = $engine;
        $this->db = $pdo;
        $this->auth = $auth;
        $this->qb = new QueryBuilder;
        $this->flash = new Flash;
    }

    public function index() {
        $this->checkLogin();
        header("Location: /users");
    }

    public function page_login() {
        echo $this->templates->render('page_login', ['title' => 'Войти', 'flash' => $this->flash]);
    }

    public function page_register() {
        echo $this->templates->render('page_register', ['title' => 'Регистрация', 'flash' => $this->flash]);
    }

    public function users() {
        $this->checkLogin();
        $users = $this->qb->getUsersInfo();
        echo $this->templates->render('users', ['users' => $users, 'flash' => $this->flash, 'auth' => $this->auth]);
    }

    public function page_profile() {
        $this->checkLogin();
        $users = $this->qb->getAll('users');
        echo $this->templates->render('page_profile', ['users' => $users, 'flash' => $this->flash]);
    }

    public function edit($id) {
        $this->checkLogin();
        if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) die('Недостаточно прав');
        $user = $this->qb->getUserInfo($id);
        echo $this->templates->render('edit', ['user' => $user, 'flash' => $this->flash, 'title' => 'Редактировать', 'id' => $id]);
    }

    public function edits() {
        $this->checkLogin();
        if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) die('Недостаточно прав');
        $user = $this->qb->getUserInfo($this->auth->getUserId());
        echo $this->templates->render('edit', ['user' => $user, 'flash' => $this->flash]);
    }

    public function edit_handler() {
        $this->checkLogin();
        $user = $this->qb->updateUserInfo($_POST['id'], $_POST);
        $this->flash->success('Профиль успешно обновлен');
        header("Location: /users");
    }

    public function medias() {
        $this->checkLogin();
        echo $this->templates->render('media');
    }

    public function media_controller() {
        $this->checkLogin();
        $user = $this->qb->updateUserInfo($this->auth->getUserId(), $_POST);
        $this->flash->success('Профиль успешно обновлен');
        header("Location: /users");
    }
    
    public function security() {
        $this->checkLogin();
        echo $this->templates->render('security');
    }

    public function status($id) {
        $this->checkLogin();
        $user = $this->qb->getUserInfo($this->auth->getUserId());
        $status = [
            "online" => "Онлайн",
            "away" => "Отошел",
            "busy" => "Не беспокоить"
        ];
        echo $this->templates->render('status', ['status' => $status, 'currentStatus' => $user['status']]);
    }

    public function status_handler() {
        $this->checkLogin();
        $this->qb->updateUserInfo($this->auth->getUserId(), $_POST);
        $this->flash->success('Статус успешно изменен');
        header("Location: /users");
        exit;
    }

    public function create_user() {
        $this->checkLogin();
        echo $this->templates->render('create_user');
    }

    public function checkLogin() {
        if (!$this->auth->isLoggedIn()) {
            $this->flash->error('Вы не авторизованы');
            header("Location: /page_login");
            exit;
        }
    }
}

