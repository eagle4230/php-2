<?php

namespace GB\CP\Blog\Commands\FakeData;

use GB\CP\Blog\Post;
use GB\CP\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GB\CP\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GB\CP\Blog\User;
use GB\CP\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    // Внедряем генератор тестовых данных и
    // репозитории пользователей и статей
    public function __construct(
        private \Faker\Generator         $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_REQUIRED,
                'Number of created users',
                1
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_REQUIRED,
                'Number of created posts',
                1
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        // Создаём пользователей
        $users = [];
        for ($i = 0; $i < $input->getOption('users-number'); $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln($i . ' User created: ' . $user->getUsername());
        }
        // От имени каждого пользователя
        // создаём по двадцать статей
        foreach ($users as $user) {
            for ($i = 0; $i < $input->getOption('posts-number'); $i++) {
                $post = $this->createFakePost($user);
                $output->writeln($i . ' Post created: ' . $post->getTitle());
            }
        }
        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
        // Генерируем логин
            $this->faker->userName,
            // Генерируем пароль
            $this->faker->password,
            // Генерируем Имя
            $this->faker->firstName,
            // Генерируем фамилию
            $this->faker->lastName
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        return $user;
    }

    private function createFakePost(User $user): Post
    {
        $post = new Post(
            UUID::random(),
            $user,
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }
}