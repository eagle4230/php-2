<?php

namespace GB\CP\Http\Actions\Likes;

use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Exceptions\InvalidArgumentException;
use GB\CP\Blog\Exceptions\LikeAlreadyExistsException;
use GB\CP\Blog\Exceptions\PostNotFoundException;
use GB\CP\Blog\Like;
use GB\CP\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\UUID;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\SuccessfulResponse;

class CreateLikePost implements ActionInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postsRepository,
    ){
    }

    /**
     * @throws InvalidArgumentException
     * @throws \JsonException
     */
    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->jsonBodyField('post_uuid');
            $authorUuid = $request->jsonBodyField('author_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForPostExists($postUuid, $authorUuid);
        } catch (LikeAlreadyExistsException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = UUID::random();

        $like = new Like(
            uuid: $newLikeUuid,
            post_uuid: new UUID($postUuid),
            author_uuid: new UUID($authorUuid),
        );

        $this->likesRepository->save($like);

        return new SuccessfulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}