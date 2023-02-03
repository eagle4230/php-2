<?php

namespace GB\CP\Blog\Commands;

use PHPUnit\Framework\TestCase;
use GB\CP\Blog\Commands\Arguments;
use GB\CP\Blog\Commands\CreateUserCommand;
use GB\CP\Blog\Exceptions\CommandException;
use GB\CP\Blog\Repositories\UsersRepository\DummyUsersRepository;

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
}