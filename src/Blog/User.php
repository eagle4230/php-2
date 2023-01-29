<?php

namespace GB\CP\Blog;

class User
{
  private int $id;
  private string $firstName;
  private string $lastName;
 
  public function __construct(string $firstName, string $lastName) {
    $this->firstName = $firstName;
    $this->lastName = $lastName;
  }

  public function getId(): int
  {
    return $this->id;
  }
  
  public function setId(int $id): void
  {
    $this->id = $id;
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

  public function setLastName(string $LastName): void
  {
    $this->lastName = $lastName;
  }
  
  public function __toString(): string
  {
    return "$this->firstName $this->lastName";
  }
}