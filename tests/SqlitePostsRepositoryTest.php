<?php

namespace GB\CP;

use GB\CP\Blog\Exceptions\PostNotFoundException;
use GB\CP\Blog\Post;
use GB\CP\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GB\CP\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);

        $this->expectExceptionMessage('Cannot find post: 8e87c563-821a-46a7-af9e-57d9af259e36');
        $this->expectException(PostNotFoundException::class);
        $repository->get(new UUID('8e87c563-821a-46a7-af9e-57d9af259e36'));
    }

    public function testItSavePostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) // Ожидаем один вызов
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':title' => 'Заголовок',
                ':text' => 'Текст, текст....'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            'name',
            'Vasy',
            'Morikov'
        );

        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Заголовок',
                'Текст, текст....'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' =>  '8e87c563-821a-46a7-af9e-57d9af259e36',
            'author_uuid' => '321e4567-e89b-12d3-a456-426614174000',
            'title' => 'Заголовок',
            'text' => 'Текст, текст...',
            'username' => 'vany23',
            'first_name' => 'Ivan',
            'last_name' => 'Smirnov'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub);
        $post = $postRepository->get(new UUID('8e87c563-821a-46a7-af9e-57d9af259e36'));

        $this->assertSame('8e87c563-821a-46a7-af9e-57d9af259e36', (string)$post->getUuid());
    }
}