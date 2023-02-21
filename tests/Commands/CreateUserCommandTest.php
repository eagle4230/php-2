<?php

namespace GB\CP\Blog\Commands;

use GB\Blog\UnitTests\DummyLogger;
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
  /* TODO public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
  {
    // Создаём объект команды
    // У команды одна зависимость - UsersRepositoryInterface
    $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    // Описываем тип ожидаемого исключения
    $this->expectException(CommandException::class);
  
    // и его сообщение
    $this->expectExceptionMessage('User already exists: Ivan');

    // Запускаем команду с аргументами
    $command->handle(new Arguments(['username' => 'Ivan']));
  }*/

    // Проверяем что команда действительно требует пароля
    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
            'username' => 'Ivan'
        ]));
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
        $command = new CreateUserCommand($usersRepository, new DummyLogger());
  
        // Ожидаем, что будет брошено исключение
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: username');
  
        // Запускаем команду
        $command->handle(new Arguments(['first_name' => 'Ivan']));
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
    $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage('No such argument: last_name');
  
    $command->handle(new Arguments([
      'username' => 'Ivan',
      'password' => '123',
      // Нам нужно передать имя пользователя,
      // чтобы дойти до проверки наличия фамилии
      'first_name' => 'Ivan',
    ]));
  }

  // Тест, проверяющий, что команда сохраняет пользователя в репозитории

    /**
     * @throws CommandException
     */
    public function testItSavesUserToRepository(): void
  {
    // Создаём объект анонимного класса
    $usersRepository = new class implements UsersRepositoryInterface
    {
      // В этом свойстве мы храним информацию о том,
      // был ли вызван метод save
      private bool $called = false;
    
      public function save(User $user): void
      {
        // Запоминаем, что метод save был вызван
        $this->called = true;
      }

      public function get(UUID $uuid): User
      {
        throw new UserNotFoundException("Not found");
      }

      public function getByUsername(string $username): User
      {
        throw new UserNotFoundException("Not found");
      }

      // Этого метода нет в контракте UsersRepositoryInterface,
      // но ничто не мешает его добавить.
      // С помощью этого метода мы можем узнать,
      // был ли вызван метод save
      public function wasCalled(): bool
      {
        return $this->called;
      }
    };

    // Передаём наш мок в команду
    $command = new CreateUserCommand($usersRepository, new DummyLogger());

    // Запускаем команду
    $command->handle(new Arguments([
      'username' => 'Ivan',
      'password' => 'some_password',
      'first_name' => 'Ivan',
      'last_name' => 'Nikitin',
    ]));

    // Проверяем утверждение относительно мока,
    // а не утверждение относительно команды
    $this->assertTrue($usersRepository->wasCalled());
  }

}