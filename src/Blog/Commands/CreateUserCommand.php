<?php

namespace GB\CP\Blog\Commands;

use GB\CP\Blog\Exceptions\ArgumentsException;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use GB\CP\Blog\Exceptions\CommandException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
  // Команда зависит от контракта репозитория пользователей,
  // а не от конкретной реализации
  public function __construct(
      private UsersRepositoryInterface $usersRepository,
      private LoggerInterface $logger,
  ) {
  }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
  {
      $this->logger->info("Create user command started");

      $username = $arguments->get('username');

      // Проверяем, существует ли пользователь в репозитории
      if ($this->userExists($username)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $username");
            // Вместо выбрасывания исключения просто выходим из функции
            return;
            // Бросаем исключение, если пользователь уже существует
            // закоментил после добавления лога
            //throw new CommandException("User already exists: $username");
      }

      $uuid = UUID::random();
      // Сохраняем пользователя в репозиторий
      $this->usersRepository->save(
          new User(
              $uuid,
              $username,
              $arguments->get('first_name'),
              $arguments->get('last_name')
          )
      );
      $this->logger->info("User created: $uuid");
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
