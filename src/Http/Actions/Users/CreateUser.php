<?php

namespace GB\CP\Http\Actions\Users;

use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\SuccessfulResponse;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();

            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('username'),
                $request->jsonBodyField('first_name'),
                $request->jsonBodyField('last_name')
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}