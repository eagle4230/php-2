<?php

namespace GB\CP\Http\Auth;

use GB\CP\Blog\User;
use GB\CP\Http\Request;

interface IdentificationInterface
{
    // Контракт описывает единственный метод,
    // получающий пользователя из запроса
    public function user(Request $request): User;
}