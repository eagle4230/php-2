<?php

namespace GB\CP\Http\Actions\Posts;

use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Exceptions\PostNotFoundException;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\UUID;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\SuccessfulResponse;

class FindByUuid implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = $this->postsRepository->get(new UUID($uuid));
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $uuid,
            'author' =>$postUuid->getUser()->getUsername(),
            'title' => $postUuid->getTitle(),
            'text' => $postUuid->getText()
        ]);
    }
}