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
/*
$usersRepository->save(new User(
  UUID::random(),
  "$faker->userName",
  "$faker->firstName",
  "$faker->lastName")
);
*/

//Извлекаем из репозитория пользователя по UUID
//echo $usersRepository->get(new UUID("0a7615e4-413e-4a00-9b21-a81bc5eebf21")) . PHP_EOL;

//Ловим исключения
try {
  ///*
  //Добавляем в репозиторий пользователя
  $usersRepository->save(new User(
    UUID::random(),
    "$faker->userName",
    "$faker->firstName",
    "$faker->lastName"
    )
  );
  //*/
  //Извлекаем из репозитория пользователя по UUID
  echo $usersRepository->get(new UUID("b229eb01-ff35-4f62-8a32-7d71840eeaa0")) . PHP_EOL;

} catch (Exception $e) {
  echo $e->getMessage();
}
