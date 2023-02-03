<?php

namespace GB\CP\Blog\Commands;

use PHPUnit\Framework\TestCase;
use GB\CP\Blog\Commands\Arguments;
use GB\CP\Blog\Commands\CreateUserCommand;
use GB\CP\Blog\Exceptions\ArgumentsException;
use GB\CP\Blog\Exceptions\CommandException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Repositories\UsersRepository\DummyUsersRepository;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\UUID;
use GB\CP\Blog\User;

class CreateUserCommandTest extends TestCase
{
  // Проверяем, что команда создания пользователя бросает исключение,
  // если пользователь с таким именем уже существует
  public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
  {
    // Создаём объект команды
    // У команды одна зависимость - UsersRepositoryInterface
    $command = new CreateUserCommand(new DummyUsersRepository);
    // Описываем тип ожидаемого исключения
    $this->expectException(CommandException::class);
  
    // и его сообщение
    $this->expectExceptionMessage('User already exists: Ivan');

    // Запускаем команду с аргументами
    $command->handle(new Arguments(['username' => 'Ivan']));
  }

  // Тест проверяет, что команда действительно требует имя пользователя
  public function testItRequiresFirstName(): void
  {
    // $usersRepository - это объект анонимного класса,
    // реализующего контракт UsersRepositoryInterface
    $usersRepository = new class implements UsersRepositoryInterface
    {
      public function save(User $user): void
      {
        // Ничего не делаем
      }

      public function get(UUID $uuid): User
      {
        // И здесь ничего не делаем
        throw new UserNotFoundException("Not found");
      }

      public function getByUsername(string $username): User
      {
        // И здесь ничего не делаем
        throw new UserNotFoundException("Not found");
      }
    };

    // Передаём объект анонимного класса
    // в качестве реализации UsersRepositoryInterface
    $command = new CreateUserCommand($usersRepository);
  
    // Ожидаем, что будет брошено исключение
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage('No such argument: first_name');
  
    // Запускаем команду
    $command->handle(new Arguments(['username' => 'Ivan']));
  }

  // Функция возвращает объект типа UsersRepositoryInterface
  private function makeUsersRepository(): UsersRepositoryInterface
  {
    return new class implements UsersRepositoryInterface
    {
      public function save(User $user): void
      {
      }

      public function get(UUID $uuid): User
      {
        throw new UserNotFoundException("Not found");
      }
  
      public function getByUsername(string $username): User
      {
        throw new UserNotFoundException("Not found");
      }
    };
  }

  // Тест проверяет, что команда действительно требует фамилию пользователя
  public function testItRequiresLastName(): void
  {
    // Передаём в конструктор команды объект, возвращаемый нашей функцией
    $command = new CreateUserCommand($this->makeUsersRepository());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage('No such argument: last_name');
  
    $command->handle(new Arguments([
      'username' => 'Ivan',
      // Нам нужно передать имя пользователя,
      // чтобы дойти до проверки наличия фамилии
      'first_name' => 'Ivan',
    ]));
  }

}
