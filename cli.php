<?php

use GB\CP\Blog\{Commands\Arguments, Commands\CreateUserCommand, Exceptions\AppException, User, Post, Comment, UUID};

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}
