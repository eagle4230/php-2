<?php

namespace GB\CP\Blog\Repositories\PostsRepository;

use GB\CP\Blog\Post;
use GB\CP\Blog\UUID;

interface PostsRepositoryInterface
{
  public function save(Post $post): void;
  public function get(UUID $uuid): Post;
  public function delete(UUID $uuid): void;

}