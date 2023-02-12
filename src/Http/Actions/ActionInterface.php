<?php

namespace GB\CP\Http\Actions;

use GB\CP\Http\Request;
use GB\CP\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}