<?php

use GB\CP\Blog\Exceptions\AppException;
use GB\CP\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GB\CP\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Http\Actions\Comments\CreateComment;
use GB\CP\Http\Actions\Likes\CreateLikePost;
use GB\CP\Http\Actions\Posts\CreatePost;
use GB\CP\Http\Actions\Posts\DeletePost;
use GB\CP\Http\Actions\Posts\FindByUuid;
use GB\CP\Http\Actions\Users\CreateUser;
use GB\CP\Http\Actions\Users\FindByUsername;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use Psr\Log\LoggerInterface;


// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

// Создаём объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
    // Читаем поток, содержащий тело запроса
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindByUuid::class,
    ],
    'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/like/create' => CreateLikePost::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],
];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    // Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];

// С помощью контейнера
// создаём объект нужного действия
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();
