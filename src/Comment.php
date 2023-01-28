<?php

namespace GB\CP;

class Comment
{
  private int $id;
  private int $idUser;
  private int $idArticle;
  private string $text;

  public function __construct(string $text){
    $this->text = $text;
  }

  public function __toString(): string
  {
    return "$this->text";
  }
}