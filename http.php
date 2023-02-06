<?php

use GB\CP\Http\Request;
use GB\CP\Http\SuccessfulResponse;

require_once __DIR__ . '/vendor/autoload.php';

// Создаём объект запроса из суперглобальных переменных
$request = new Request($_GET, $_SERVER);

//$parameter = $request->query('some_parameter');
//$header = $request->header('Some-Header');
//$path = $request->path();

// Создаём объект ответа
$response = new SuccessfulResponse([
    'name' => 'Alex',
    'age' => '43',
]);

// Отправляем ответ
$response->send();
