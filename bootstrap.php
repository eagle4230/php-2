<?php

use Dotenv\Dotenv;
use GB\CP\Blog\Container\DIContainer;
use GB\CP\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GB\CP\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GB\CP\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use GB\CP\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();

// 1. подключение к БД
$container->bind(
    PDO::class,
    // Берём путь до файла базы данных SQLite
    // из переменной окружения SQLITE_DB_PATH
    new PDO('sqlite:' . __DIR__ . '/' . $_ENV['SQLITE_DB_PATH'])
);

// для логирования
$container->bind(
    LoggerInterface::class,
    (new Logger('blog'))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        // Добавили новый обработчик:
        ->pushHandler(new StreamHandler(
        // записывать в файл "blog.error.log"
            __DIR__ . '/logs/blog.error.log',
        // события с уровнем ERROR и выше,
            level: Logger::ERROR,
        // при этом событие не должно "всплывать"
            bubble: false,
        ))
        // Добавили ещё один обработчик;
        // он будет вызываться первым ... т.к. читает с конца
        ->pushHandler(
            // .. и вести запись в поток php://stdout,
            // то есть в консоль
            new StreamHandler("php://stdout")
        )
);

// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

// 4. репозиторий комментариев
$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

// 5. репозиторий лайков
$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

// Возвращаем объект контейнера
return $container;

