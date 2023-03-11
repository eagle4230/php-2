<?php

use Dotenv\Dotenv;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use GB\CP\Blog\Container\DIContainer;
use GB\CP\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GB\CP\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use GB\CP\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GB\CP\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GB\CP\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use GB\CP\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Http\Actions\Auth\BearerTokenAuthentication;
use GB\CP\Http\Auth\AuthenticationInterface;
use GB\CP\Http\Auth\JsonBodyUsernameIdentification;
use GB\CP\Http\Auth\PasswordAuthentication;
use GB\CP\Http\Auth\PasswordAuthenticationInterface;
use GB\CP\Http\Auth\TokenAuthenticationInterface;
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

// Выносим объект логгера в переменную
$logger = (new Logger('blog'));
// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}
// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger->pushHandler(
            new StreamHandler("php://stdout")
    );
}

$container->bind(
    AuthenticationInterface::class,
    JsonBodyUsernameIdentification::class
);

$container->bind(
    AuthenticationInterface::class,
    JsonBodyUsernameIdentification::class
);

// для логирования
$container->bind(
    LoggerInterface::class,
    $logger
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

// 6. для аутентификации
$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

// Создаём объект генератора тестовых данных
$faker = new \Faker\Generator();
// Инициализируем необходимые нам виды данных
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));
// Добавляем генератор тестовых данных
// в контейнер внедрения зависимостей
$container->bind(
    \Faker\Generator::class,
    $faker
);


// Возвращаем объект контейнера
return $container;

