<?php

namespace GB\CP\Blog;

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

  public function getId(): int
  {
    return $this->id;
  }
  
  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getIdUser(): int
  {
    return $this->idUser;
  }

  public function setIdUser(int $idUser): void
  {
    $this->idUser = $idUser;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function setTitle(string $title): void
  {
    $this->title = $title;
  }

  public function getText(): string
  {
    return $this->text;
  }

  public function setText(string $text): void
  {
    $this->text = $text;
  }

  public function __toString(): string
  {
    return "$this->title >>> $this->text";
  }
}