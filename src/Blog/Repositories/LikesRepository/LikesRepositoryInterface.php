<?php

namespace GB\CP\Blog\Repositories\LikesRepository;

use GB\CP\Blog\Like;
use GB\CP\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $uuid): array;
}