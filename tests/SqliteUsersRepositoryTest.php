<?php

namespace GB\CP;

use GB\Blog\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;

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

    $repository = new SqliteUsersRepository($connectionMock, new DummyLogger());
    $this->expectException(UserNotFoundException::class);
    $this->expectExceptionMessage('Cannot find user: Ivan');

    $repository->getByUsername('Ivan');

  }

  // Тест, проверяющий, что репозиторий сохраняет данные в БД
  public function testItSavesUserToDatabase(): void
  {
    // 2. Создаём стаб подключения
    $connectionStub = $this->createStub(PDO::class);

    // 4. Создаём мок запроса, возвращаемый стабом подключения
    $statementMock = $this->createMock(PDOStatement::class);
  
    // 5. Описываем ожидаемое взаимодействие
    // нашего репозитория с моком запроса
    $statementMock
      ->expects($this->once()) // Ожидаем, что будет вызван один раз
      ->method('execute') // метод execute
      ->with([ // с единственным аргументом - массивом
        ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':username' => 'ivan123',
        ':password' => 'some_password',
        ':first_name' => 'Ivan',
        ':last_name' => 'Nikitin',
      ]);
    
    // 3. При вызове метода prepare стаб подключения
    // возвращает мок запроса
    $connectionStub->method('prepare')->willReturn($statementMock);

    // 1. Передаём в репозиторий стаб подключения
    $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());

    // Вызываем метод сохранения пользователя
    $repository->save(
      new User( // Свойства пользователя точно такие,
                // как и в описании мока
      new UUID('123e4567-e89b-12d3-a456-426614174000'),
      'ivan123',
      'some_password',
      'Ivan', 
      'Nikitin'
      )
    );
  }

}