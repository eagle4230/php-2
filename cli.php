<?php

use GB\CP\Blog\{User, Post, Comment, UUID};
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);

//Добавляем в репозиторий пользователя
$usersRepository->save(new User(UUID::random(), "$faker->firstName", "$faker->lastName"));
