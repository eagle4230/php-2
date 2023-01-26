<?php

require_once __DIR__ . '/vendor/autoload.php';

use GB\CP\User;
use GB\CP\Post;
use GB\CP\Comment;

$faker = Faker\Factory::create('ru_RU');

if ($argv[1] == "user") {
  $user = new User($faker->firstName(), $faker->lastName());
  echo $user . PHP_EOL;
}

if ($argv[1] == "post") {
  $post = new Post($faker->realText($maxNbChars = 60), $faker->realText($maxNbChars = 320));
  echo $post . PHP_EOL;
}

if ($argv[1] == "comment") {
  $comment = new Comment($faker->realText($maxNbChars = 180));
  echo $comment . PHP_EOL;
}