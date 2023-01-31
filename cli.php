<?php

use GB\CP\Blog\{User, Post, Comment, UUID};
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Blog\Commands\CreateUserCommand;
use GB\CP\Blog\Commands\Exceptions\CommandException;


require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);

$command = new CreateUserCommand($usersRepository);

try {
  /*
  //Добавляем в репозиторий пользователя
  $usersRepository->save(new User(
    UUID::random(),
    "$faker->userName",
    "$faker->firstName",
    "$faker->lastName"
    )
  );
  */

  //Извлекаем из репозитория пользователя по UUID
  //echo $usersRepository->get(new UUID("54a416a8-f974-4ea0-8a1c-0814f1eba189")) . PHP_EOL;

  //Извлекаем из репозитория пользователя по username
  //echo $usersRepository->getByUsername('taksenov') . PHP_EOL;

  $command->handle($argv);

} catch (Exception $e) {
  echo $e->getMessage() . PHP_EOL;
}
