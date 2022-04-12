<?php
if( !session_id() ) @session_start();
require '../vendor/autoload.php';

use App\QueryBuilder;
use Aura\SqlQuery\QueryFactory;
use JasonGrimes\Paginator;
use App\controllers\HomeController;
use \Tamtamchik\SimpleFlash\Flash;
use \DI\ContainerBuilder;
use \Delight\Auth\Auth;
use League\Plates\Engine;

$builder = new ContainerBuilder;
$builder->addDefinitions([
    Engine::class    =>  function() {
        return new Engine('../app/views');
    },

    PDO::class => function() {
        return new PDO('mysql:host=localhost;dbname=host1380688_marlindev', 'host1380688_marlindev', 'marlindev');
    },

    Auth::class   =>  function($container) {
        return new Auth($container->get('PDO'));
    },

    QueryFactory::class  =>  function() {
        return new QueryFactory('mysql');
    }
]);
$container = $builder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\controllers\HomeController', 'page_login']);
    $r->addRoute('GET', '/test', ['App\controllers\HomeController', 'test']);
    $r->addRoute('GET', '/index', ['App\controllers\HomeController', 'index']);
    $r->addRoute('GET', '/page_login', ['App\controllers\HomeController', 'page_login']);
    $r->addRoute('GET', '/page_register', ['App\controllers\HomeController', 'page_register']);
    $r->addRoute('POST', '/login', ['App\controllers\UserController', 'login']);
    $r->addRoute('GET', '/logout', ['App\controllers\UserController', 'logout']);
    $r->addRoute('POST', '/register', ['App\controllers\HomeController', 'register']);
    $r->addRoute('GET', '/users', ['App\controllers\HomeController', 'users']);
    $r->addRoute('GET', '/page_profile', ['App\controllers\HomeController', 'page_profile']);
    $r->addRoute('GET', '/edit/{id:\d+}', ['App\controllers\HomeController', 'edit']);
    $r->addRoute('GET', '/edits', ['App\controllers\HomeController', 'edits']);
    $r->addRoute('POST', '/edit_handler', ['App\controllers\HomeController', 'edit_handler']);
    $r->addRoute('POST', '/status_handler', ['App\controllers\HomeController', 'status_handler']);
    $r->addRoute('GET', '/medias', ['App\controllers\HomeController', 'medias']);
    $r->addRoute('GET', '/security', ['App\controllers\HomeController', 'security']);
    $r->addRoute('GET', '/status/{id:\d+}', ['App\controllers\HomeController', 'status']);
    $r->addRoute('GET', '/create_user', ['App\controllers\HomeController', 'create_user']);
    $r->addRoute('POST', '/create_user_handler', ['App\controllers\UserController', 'create_user_handler']);
    $r->addRoute('GET', '/role', ['App\controllers\UserController', 'role']);
    $r->addRoute('GET', '/user_delete/{id:\d+}', ['App\controllers\UserController', 'user_delete']);
    $r->addRoute('GET', '/getusers', ['App\controllers\UserController', 'getusers']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        var_dump($_SERVER['REQUEST_URI'], $uri);
        echo '404 page';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo $_SERVER['REQUEST_URI'] . ' > ' . $uri . ' > ' . $httpMethod;
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($handler, $vars);
        break;
}


