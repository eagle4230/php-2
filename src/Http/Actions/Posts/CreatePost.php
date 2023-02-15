<?php

namespace GB\CP\Http\Actions\Posts;

use GB\CP\Blog\Exceptions\AuthException;
use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Post;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\UUID;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\Auth\AuthenticationInterface;
use GB\CP\Http\Auth\IdentificationInterface;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private AuthenticationInterface $authentication,
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger //Внедряем контракт логгера
    )
    {
    }
    public function handle(Request $request): Response
    {
        // Обрабатываем ошибки аутентификации
        // и возвращаем неудачный ответ
        // с сообщением об ошибке
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }


        $user = $this->authentication->user($request);

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