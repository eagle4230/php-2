<?php

class Article
{
  private int $id;
  private int $idUser;
  private string $title;
  private string $text;

  public function __construct(int $id, int $idUser, string $title, string $text){
    $this->id = $id;
    $this->idUser = $idUser;
    $this->title = $title;
    $this->text = $text;
  }

  public function getId(): int
  {
    return $this->$id;
  }

  public function getIdUser(): int
  {
    return $this->$idUser;
  }
  
  public function getTitle(): string
  {
    return $this->$title;
  }
  
  public function getText(): string
  {
    return $this->$text;
  }
}