<?php

namespace GB\CP\Blog\Repositories\UsersRepository;

use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use PDO;

class SqliteUsersRepository
{
  private PDO $connection;

  public function __construct(PDO $connection) {
    $this->connection = $connection;
  }

  public function save(User $user): void
  {
    // Подготавливаем запрос
    $statement = $this->connection->prepare(
      'INSERT INTO users (uuid, first_name, last_name) VALUES (:uuid, :first_name, :last_name)'
    );

    // Выполняем запрос с конкретными значениями
    $statement->execute([
      ':uuid' => $user->getUUID(),
      ':first_name' => $user->getFirstName(),
      ':last_name' => $user->getLastName()
    ]);

  }

}