<?php

namespace GB\CP\Blog\Container;

use GB\CP\Blog\Exceptions\NotFoundException;

class DIContainer
{
    // Массив правил создания объектов
    private array $resolvers = [];

    // Метод для добавления правил
    public function bind(string $type, $resolver)
    {
        $this->resolvers[$type] = $resolver;
    }

    public function get(string $type): object
    {
        if (array_key_exists($type, $this->resolvers)) {
            $typeToCreate = $this->resolvers[$type];

            // Если в контейнере для запрашиваемого типа
            // уже есть готовый объект — возвращаем его
            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        if (!class_exists($type)) {
            throw new NotFoundException("Cannot resolve type: $type");
        }

        return new $type();
    }

}