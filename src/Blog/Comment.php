<?php

namespace GB\CP\Blog;

class Comment
{
  public function __construct(
    private UUID $uuid,
    private Post $post,
    private User $user,
    private string $text
  )
  {
  }

  public function getUuid(): UUID
  {
    return $this->uuid;
  }
  
  public function setUuid(UUID $uuid): void
  {
    $this->uuid = $uuid;
  }

  public function getUser(): User
  {
    return $this->user;
  }

  public function getPost(): Post
  {
    return $this->post;
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