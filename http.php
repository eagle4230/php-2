<?php

use GB\CP\Blog\Exceptions\AppException;
use GB\CP\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Http\Actions\Users\CreateUser;
use GB\CP\Http\Actions\Users\FindByUsername;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\SuccessfulResponse;

require_once __DIR__ . '/vendor/autoload.php';

// Создаём объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
    // Читаем поток, содержащий тело запроса
    file_get_contents('php://input'),
);

try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
    // Отправляем неудачный ответ,
    // если по какой-то причине
    // не можем получить путь
    (new ErrorResponse)->send();
    // Выходим из программы
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    (new ErrorResponse)->send();
    return;
}

$routes = [
    // Добавили ещё один уровень вложенности
    // для отделения маршрутов,
    // применяемых к запросам с разными методами
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        //TODO
        //'/posts/show' => new FindByUuid(
        //    new SqlitePostsRepository(
        //        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
        //    )
        //),
    ],
    'POST' => [
        '/users/create' => new CreateUser(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        //TODO
        //'/posts/create' => new CreatePost(
        //    new SqlitePostsRepository(
        //        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
        //    ),
        //    new SqliteUsersRepository(
        //        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
        //    )
        //),
    ],
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    // Пытаемся выполнить действие,
    // при этом результатом может быть
    // как успешный, так и неуспешный ответ
    $response = $action->handle($request);
    // Отправляем ответ
    $response->send();
} catch (Exception $e) {
    // Отправляем неудачный ответ,
    // если что-то пошло не так
    (new ErrorResponse($e->getMessage()))->send();
}
