<?php

namespace GB\CP\Blog\Repositories\PostsRepository;

use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\Exceptions\PostsRepositoryException;
use GB\CP\Blog\Post;
use GB\CP\Blog\UUID;
use \PDO;
use PDOException;
use \PDOStatement;
use GB\CP\Blog\Exceptions\PostNotFoundException;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    private PDO $connection;
    public function __construct(
        PDO $connection,
        private LoggerInterface $logger
    )
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

        // Пишем в лог-файл
        $uuid = $post->getUuid();
        $this->logger->info("Post saved under UUID: $uuid");
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
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

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    private function getPost(PDOStatement $statement, string $postUuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Not found Post with UUID: $postUuid");
            throw new PostNotFoundException(
                "Cannot find post: $postUuid" . PHP_EOL
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->get(new UUID($result['author_uuid']));
 
        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );

    }

    public function delete(UUID $uuid): void
    {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE uuid = ?'
            );
            $statement->execute([(string)$uuid]);
        } catch (PDOException $e) {
            throw new PostsRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }


}