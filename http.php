<?php

use GB\CP\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

// Создаём объект запроса из суперглобальных переменных
$request = new Request($_GET, $_SERVER);

$path = $request->path();