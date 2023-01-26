<?php

namespace GB\CP;

class User
{
  private int $id;
  private string $name;
  private string $lastName;
 
  public function __construct(string $name, string $lastName) {
    $this->name = $name;
    $this->lastName = $lastName;
  }

  public function __toString(): string
  {
    return "$this->name $this->lastName";
  }
}