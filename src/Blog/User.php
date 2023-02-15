<?php

namespace GB\CP\Blog;

use GB\CP\Blog\UUID;

class User
{
  private UUID $uuid;
  private string $username;
  private string $hashedPassword;
  private string $firstName;
  private string $lastName;

  public function __construct(
    UUID $uuid, 
    string $username,
    string $hashedPassword,
    string $firstName, 
    string $lastName,
  )
  {
    $this->uuid = $uuid;
    $this->username = $username;
    $this->hashedPassword = $hashedPassword;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
  }

  public function getUUID(): UUID
  {
    return $this->uuid;
  }
  
  public function setUUID(UUID $uuid): void
  {
    $this->uuid = $uuid;
  }

  public function getUsername(): string
  {
    return $this->username;
  }

  public function setUsername(string $username): void
  {
    $this->username = $username;
  }

  public function hashedPassword(): string
  {
      return $this->hashedPassword;
  }

    // Функция для вычисления хеша
    private static function hash(string $password): string
    {
        return hash('sha256', $password);
    }

    // Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password);
    }

    // Функция для создания нового пользователя
    public static function createFrom(
        string $username,
        string $password,
        string $firstName,
        string $lastName
    ): self
    {
        return new self(
            UUID::random(),
            $username,
            self::hash($password),
            $firstName,
            $lastName
        );
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

  public function setFirstName(string $firstName): void
  {
    $this->firstName = $firstName;
  }

  public function getLastName(): string
  {
    return $this->lastName;
  }

  public function setLastName(string $lastName): void
  {
    $this->lastName = $lastName;
  }
  
  public function __toString(): string
  {
    return "UUID: $this->uuid" . PHP_EOL . "Login: $this->username" . PHP_EOL .
        "Имя: $this->firstName" . PHP_EOL . "Фамилия: $this->lastName";
  }
}