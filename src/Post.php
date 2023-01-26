<?php

namespace GB\CP;

class Post
{
  private int $id;
  private int $idUser;
  private string $title;
  private string $text;

  public function __construct(string $title, string $text){
    $this->title = $title;
    $this->text = $text;
  }

  public function __toString(): string
  {
    return "$this->title >>> $this->text";
  }
}