<?php

namespace GB\CP\Blog\Repositories\CommentsRepository;

use GB\CP\Blog\{Comment,
    Exceptions\CommentNotFoundException,
    Exceptions\InvalidArgumentException,
    Exceptions\PostNotFoundException,
    Repositories\PostsRepository\SqlitePostsRepository,
    Repositories\UsersRepository\SqliteUsersRepository,
    UUID};
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    private PDO $connection;
    public function __construct(
        PDO $connection,
        private LoggerInterface $logger
    )
    {
        $this->connection = $connection;
    }
    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
      'INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post_uuid' => $comment->getPost()->getUuid(),
            ':author_uuid' => $comment->getUser()->getUUID(),
            ':text' => $comment->getText(),
        ]);

      // Пишем в лог-файл
      $uuid = $comment->getUuid();
      $this->logger->info("Comment saved under UUID: $uuid");
  }

    /**
     * @throws InvalidArgumentException
     * @throws CommentNotFoundException
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid): Comment
  {
      $statement = $this->connection->prepare(
          'SELECT * FROM comments WHERE uuid = :uuid'
      );
      $statement->execute([
          ':uuid' => (string)$uuid,
      ]);

      return $this->getComment($statement, $uuid);
  }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    private function getComment(PDOStatement $statement, string $commentUuid): Comment
  {
      $result = $statement->fetch(PDO::FETCH_ASSOC);

      if ($result === false) {
          $this->logger->warning("Not found Comment with UUID: $commentUuid");
          throw new CommentNotFoundException(
              "Cannot find comment: $commentUuid" . PHP_EOL
          );
      }

      $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
      $user = $userRepository->get(new UUID($result['author_uuid']));

      $postRepository = new SqlitePostsRepository($this->connection, $this->logger);
      $post = $postRepository->get(new UUID($result['post_uuid']));

      return new Comment(
          new UUID($result['uuid']),
          $post,
          $user,
          $result['text'],
      );
  }
}