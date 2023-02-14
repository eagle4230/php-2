<?php

namespace GB\CP\Http\Actions\Comments;

use GB\CP\Blog\Exceptions\CommentNotFoundException;
use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GB\CP\Blog\UUID;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\SuccessfulResponse;

class FindCommentByUuid implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
    ){
    }
    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $commentUuid = $this->commentsRepository->get(new UUID($uuid));
        } catch (CommentNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $uuid,
            'post_uuid' => (string)$commentUuid->getPost()->getUuid(),
            'username' => $commentUuid->getUser()->getUsername(),
            'text' => $commentUuid->getText()

        ]);
    }
}