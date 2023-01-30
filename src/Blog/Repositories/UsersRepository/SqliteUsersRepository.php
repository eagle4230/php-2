<?php

namespace GB\CP\Blog\Repositories\UsersRepository;

use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use \PDO;

class SqliteUsersRepository implements UsersRepositoryInterface
{
  private PDO $connection;

  public function __construct(PDO $connection) {
    $this->connection = $connection;
  }

  public function save(User $user): void
  {
    // Подготавливаем запрос
    $statement = $this->connection->prepare(
      'INSERT INTO users (uuid, username, first_name, last_name) 
      VALUES (:uuid, :username, :first_name, :last_name)'
    );

    // Выполняем запрос с конкретными значениями
    $statement->execute([
      ':uuid' => (string)$user->getUUID(),
      ':username' => $user->getUsername(),
      ':first_name' => $user->getFirstName(),
      ':last_name' => $user->getLastName()
    ]);
  }

  // Также добавим метод для получения
  // пользователя по его UUID
  public function get(UUID $uuid): User
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM users WHERE uuid = :uuid'
    );

    $statement->execute([
      ':uuid' => (string)$uuid
    ]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);
  
    // Бросаем исключение, если пользователь не найден
    if ($result === false) {
      throw new UserNotFoundException("Cannot get user: $uuid" . PHP_EOL);
    }

    return new User(
      new UUID($result['uuid']),
      $result['username'], 
      $result['first_name'], 
      $result['last_name']
    );
  } 
}