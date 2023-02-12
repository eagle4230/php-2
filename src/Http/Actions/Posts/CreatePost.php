<?php

namespace GB\CP\Http\Actions\Posts;

use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Post;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\UUID;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger,
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}