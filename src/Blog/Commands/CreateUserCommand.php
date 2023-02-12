<?php

namespace GB\CP\Blog\Commands;

use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use GB\CP\Blog\Exceptions\CommandException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\Commands\Arguments;

class CreateUserCommand
{
  // Команда зависит от контракта репозитория пользователей,
  // а не от конкретной реализации
  public function __construct(
    private UsersRepositoryInterface $usersRepository
  ) {
  }

  public function handle(Arguments $arguments): void
  {
    $username = $arguments->get('username');

    // Проверяем, существует ли пользователь в репозитории
    if ($this->userExists($username)) {
      // Бросаем исключение, если пользователь уже существует
      throw new CommandException("User already exists: $username");
    }

    // Сохраняем пользователя в репозиторий
    $this->usersRepository->save(
      new User(
        UUID::random(),
        $username, 
        $arguments->get('first_name'), 
        $arguments->get('last_name')
      )
    );
  }
  
  private function userExists(string $username): bool
  {
    try {
      // Пытаемся получить пользователя из репозитория
      $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException) {
      return false;
    }
    return true;
  }

}
