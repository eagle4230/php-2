<?php

use GB\CP\Blog\Commands\Users\CreateUser;
use Symfony\Component\Console\Application;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);

    // Добавляем команду к приложению
    $application->add($command);
}

// Запускаем приложение
$application->run();
