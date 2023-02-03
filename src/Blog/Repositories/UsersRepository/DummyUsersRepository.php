<?php

namespace GB\CP\Blog\Repositories\UsersRepository;

use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;

class DummyUsersRepository implements UsersRepositoryInterface
{
  public function save(User $user): void
  {
    //TODO
  }

  public function get(UUID $uuid): User
  {
    throw new UserNotFoundException("Not found");
  }

  public function getByUsername(string $username): User
  {
    return new User(UUID::random(), "user123", "Artem", "Borisov");
  }
}