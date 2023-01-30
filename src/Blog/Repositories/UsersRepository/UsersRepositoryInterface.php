<?php

namespace GB\CP\Blog\Repositories\UsersRepository;

use GB\CP\Blog\{User, UUID};

interface UsersRepositoryInterface
{
  public function save(User $user): void;
  public function get(UUID $uuid): User;
}
