<?php

namespace GB\CP\Blog\Repositories\CommentsRepository;

use GB\CP\Blog\Comment;
use GB\CP\Blog\UUID;

interface CommentsRepositoryInterface
{
  public function save(Comment $comment): void;
  public function get(UUID $uuid): Comment;
}