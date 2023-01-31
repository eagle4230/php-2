<?php

namespace GB\CP\Blog\Repositories\PostsRepository;

use GB\CP\Blog\Post;
use GB\CP\Blog\UUID;
use \PDO;
use \PDOStatement;
use GB\CP\Blog\Exceptions\PostNotFoundException;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;

class SqlitePostsRepository implements PostsRepositoryInterface
{

  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function save(Post $post): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO posts (uuid, author_uuid, title, text) 
      VALUES (:uuid, :author_uuid, :title, :text)'
    );

    $statement->execute([
      ':uuid' => $post->getUuid(),
      ':author_uuid' => $post->getUser()->getUUID(),
      ':title' => $post->getTitle(),
      ':text' => $post->getText()
    ]);
  }

  public function get(UUID $uuid): Post
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM posts WHERE uuid = :uuid'
    );
    $statement->execute([
      ':uuid' => (string)$uuid,
    ]);

    return $this->getPost($statement, $uuid);

  }

  private function getPost(PDOStatement $statement, string $postUuid): Post
  {
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
      throw new PostNotFoundException(
        "Cannot find post: $postUuid" . PHP_EOL
      );
    }

    $userRepository = new SqliteUsersRepository($this->connection);
    $user = $userRepository->get(new UUID($result['author_uuid'])); 
 
    return new Post(
      new UUID($result['uuid']),
      $user,
      $result['title'],
      $result['text']
    );
  }

}