<?php

namespace GB\CP\Blog\Repositories\UsersRepository;

use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use \PDO;
use \PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    private PDO $connection;
    public function __construct(
        PDO $connection,
        private LoggerInterface $logger
    ) {
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

    // Пишем в лог-файл
      $uuid = $user->getUUID();
      $this->logger->info("User saved under UUID: $uuid");
  }

  // Также добавим метод для получения
  // пользователя по его UUID
    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM users WHERE uuid = :uuid'
    );

    $result = $statement->execute([
      ':uuid' => (string)$uuid
    ]);

    return $this->getUser($statement, $uuid);

  }


  public function getByUsername(string $username): User
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM users WHERE username = :username'
    );

    $statement->execute([
      ':username' => $username,
    ]);
    
    return $this->getUser($statement, $username); 
  }


    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, string $uuidString): User
  {
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        $this->logger->warning("Not found User with UUID: $uuidString");
        throw new UserNotFoundException("Cannot find user: $uuidString");
    }

    return new User(
      new UUID($result['uuid']),
      $result['username'], 
      $result['first_name'], 
      $result['last_name']
    );
  }

}