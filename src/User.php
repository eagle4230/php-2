<?php

namespace GB\CP;

class User
{
  private int $id;
  private string $name;
  private string $lastName;
 
  public function __construct(int $id, string $name, string $lastName) {
    $this->id = $id;
    $this->name = $name;
    $this->lastName = $lastName;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }
  
  public function getLastName(): string
  {
    return $this->lastName;
  }
}