<?php

use GB\CP\Blog\Container\DIContainer;
use GB\CP\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GB\CP\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GB\CP\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use GB\CP\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

// Создаём объект контейнера ..
$container = new DIContainer();

// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
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

