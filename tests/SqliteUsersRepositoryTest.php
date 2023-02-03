<?php

namespace GB\CP;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;

class SqliteUsersRepositoryTest extends TestCase
{
  // Тест, проверяющий, что SQLite-репозиторий бросает исключение,
  // когда запрашиваемый пользователь не найден
  public function testItThrowsAnExceptionWhenUserNotFound(): void
  {
    $connectionMock = $this->createStub(PDO::class);
    $statementStub = $this->createStub(PDOStatement::class);
    $statementStub->method('fetch')->willReturn(false);
    $connectionMock->method('prepare')->willReturn($statementStub);

    $repository = new SqliteUsersRepository($connectionMock);
    $this->expectException(UserNotFoundException::class);
    $this->expectExceptionMessage('Cannot find user: Ivan');

    $repository->getByUsername('Ivan');

  }
}