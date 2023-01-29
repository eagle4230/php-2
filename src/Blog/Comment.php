<?php

namespace GB\CP\Blog;

class Comment
{
  private int $id;
  private int $idUser;
  private int $idPost;
  private string $text;

  public function __construct(string $text){
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

  public function getIdPost(): int
  {
    return $this->idPost;
  }
  
  public function setIdPost(int $idPost): void
  {
    $this->idPost = $idPost;
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
    return "$this->text";
  }
}