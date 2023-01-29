<?php

use GB\CP\Blog\{User, Post, Comment};

include __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

switch ($argv[1]) {
  case "user":
    $user = new User($faker->firstName(), $faker->lastName());
    echo $user . PHP_EOL;
    break;
  case "post":
    $post = new Post($faker->realText($maxNbChars = 60), $faker->realText($maxNbChars = 320));
    echo $post . PHP_EOL;
    break;
  case "comment":
    $comment = new Comment($faker->realText($maxNbChars = 180));
    echo $comment . PHP_EOL;
    break;
  default:
    echo "Нет аргументов!!!";
}
