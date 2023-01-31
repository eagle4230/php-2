<?php

namespace GB\CP\Blog\Repositories\CommentsRepository;

use GB\CP\Blog\{User, Post, UUID};
use \PDO;
use \PDOStatement;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{

  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function save(Comment $comment): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
       VALUES (:uuid, :post_uuid, :author_uuid, :text)'
    );

    // TODO: statement->execute
  }

  public function get(UUID $uuid): Comment
  {
    // TODO: Релизовать метод get()
  }

}