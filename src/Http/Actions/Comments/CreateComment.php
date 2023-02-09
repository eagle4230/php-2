<?php

namespace GB\CP\Http\Actions\Comments;

use GB\CP\Blog\Comment;
use GB\CP\Blog\Exceptions\CommentNotFoundException;
use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\UUID;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\SuccessfulResponse;

class CreateComment implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository
    ) {
    }
    public function handle(Request $request): Response
    {
        // Пытаемся вытащить UUID юзера из тела запроса
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Пытаемся найти юзера в репозитории
        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Пытаемся вытащить UUID поста из тела запроса
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти пост в репозитории
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (CommentNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $newCommentUuid = UUID::random();

        try {
            $comment = new Comment(
                $newCommentUuid,
                $post,
                $user,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Сохраняем комментарий в репозиторий
        $this->commentsRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}