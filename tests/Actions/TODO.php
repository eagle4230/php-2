<?php

namespace Actions;

use GB\CP\Http\Actions\Users\FindByUsername;
use GB\CP\Http\ErrorResponse;
use GB\CP\Http\Request;
use GB\CP\Blog\Exceptions\UserNotFoundException;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use JsonException;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{
    // Запускаем тест в отдельном процессе
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    // Тест, проверяющий, что будет возвращён неудачный ответ,
    // если в запросе нет параметра username
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        // Создаём объект запроса
        // Вместо суперглобальных переменных
        // передаём простые массивы
        $request = new Request([], []);

        // Создаём стаб репозитория пользователей
        $usersRepository = $this->usersRepository();

        //Создаём объект действия
        $action = new FindByUsername($usersRepository);

        // Запускаем действие
        $response = $action->handle($request);

        // Проверяем, что ответ - неудачный
        $this->assertInstanceOf(ErrorResponse::class, $response);

        // Описываем ожидание того, что будет отправлено в поток вывода
        $this->expectOutputString('{"success":false,"reason": "No such query param in the request: username"}');

        // Отправляем ответ в поток вывода
        $response->send();
    }

    // Функция, создающая стаб репозитория пользователей,
    // принимает массив "существующих" пользователей
    private function usersRepository(): UsersRepositoryInterface
    {
        $users = [];
        // В конструктор анонимного класса передаём массив пользователей
        return new class($users) implements UsersRepositoryInterface {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->getUsername()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }
}
