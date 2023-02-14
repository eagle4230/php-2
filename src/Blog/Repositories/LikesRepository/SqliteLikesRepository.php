<?php

namespace GB\CP\Blog\Repositories\LikesRepository;

use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\Exceptions\LikeAlreadyExistsException;
use GB\CP\Blog\Exceptions\LikesNotFoundException;
use GB\CP\Blog\Like;
use GB\CP\Blog\UUID;
use PDO;
use Psr\Log\LoggerInterface;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    private PDO $connection;

    public function __construct(
        PDO $connection,
        private LoggerInterface $logger
    )
    {
        $this->connection = $connection;
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare('
            INSERT INTO likes (uuid, author_uuid, post_uuid)
            VALUES (:uuid, :author_uuid, :post_uuid)
        ');
        $statement->execute([
            ':uuid' => (string)$like->getUuid(),
            ':author_uuid' => (string)$like->getAuthorUuid(),
            ':post_uuid' => (string)$like->getPostUuid()
        ]);

        // Пишем в лог-файл
        $uuid = $like->getUuid();
        $this->logger->info("Like saved under UUID: $uuid");
    }

    /**
     * @throws LikesNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByPostUuid(UUID $uuid): array
    {
       $statement = $this->connection->prepare(
       'SELECT * FROM likes WHERE post_uuid = :uuid'
       );

       $statement->execute([
           'uuid' => (string)$uuid
       ]);

       $result = $statement->fetchAll(PDO::FETCH_ASSOC);

       if (!$result) {
           throw new LikesNotFoundException(
               'No Likes to Post with uuid = : ' . $uuid
           );
       }

       $likes = [];
       foreach ($result as $like) {
           $like[] = new Like(
               uuid: new UUID($like['uuid']),
               post_uuid: new UUID($like['post_uuid']),
               author_uuid: new UUID($like['author_uuid'])
           );
       }

       return $likes;
    }

    /**
     * @throws LikeAlreadyExistsException
     */
    public function checkUserLikeForPostExists($postUuid, $userUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :postUuid AND author_uuid = :authorUuid'
        );

        $statement->execute([
           ':postUuid' => $postUuid,
           ':authorUuid' => $userUuid
        ]);

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExistsException(
                'The Users Like for this Post already exists'
            );
        }
    }
}