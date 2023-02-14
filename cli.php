<?php

use GB\CP\Blog\{Commands\Arguments, Commands\CreateUserCommand, Exceptions\AppException, User, Post, Comment, UUID};
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Получаем Logger
$logger = $container->get(LoggerInterface::class);

try {
    // При помощи контейнера создаём команду
    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo "{$e->getMessage()}\n";
}
