<?php

class Comment
{
  private int $id;
  private int $idUser;
  private int $idArticle;
  private string $text;

  public function __construct(int $id, int $idUser, int $idArticle, string $text){
    $this->id = $id;
    $this->idUser = $idUser;
    $this->idArticle = $idArticle;
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
  
  public function getIdArticle(): int
  {
    return $this->$idArticle;
  }

    public function getText(): string
  {
    return $this->$text;
  }
}