<?php

namespace GB\CP\Http\Actions\Users;

use GB\CP\Blog\Exceptions\CommandException;
use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
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
        $username = $request->jsonBodyField('username');
        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            //$this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
        }

        try {
            $newUserUuid = UUID::random();

            $user = new User(
                $newUserUuid,
                $username,
                $request->jsonBodyField('password'),
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

    private function userExists(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}