<?php

namespace GB\CP\Blog\Repositories\CommentsRepository;

interface CommentsRepositoryInterface
{
  public function save(Comment $coment): void;
  public function get(UUID $uuid): Comment;
}