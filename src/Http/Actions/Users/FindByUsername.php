<?php

namespace GB\CP\Http\Actions\Users;

use GB\CP\Blog\Exceptions\HttpException;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Http\Actions\ActionInterface;
use GB\CP\Http\Request;
use GB\CP\Http\Response;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь не найден -
            // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->getUsername(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName()
        ]);

    }
}