<?php

require_once __DIR__ . '/vendor/autoload.php';

use GB\CP\User;
use GB\CP\Article;
use GB\CP\Comment;

$vasy = new User(1, 'Vasy', 'Pupkin');
//var_dump($vasy);
echo $vasy->getLastName() . PHP_EOL;

$art = new Article(1, 1, 'Use composer', 'Install ...');
//var_dump($art);
echo $art->getTitle() . PHP_EOL;

$com = new Comment(1, 1, 1, 'Cool!');
//var_dump($com);
echo $com->getText() . PHP_EOL;