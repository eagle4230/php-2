<?php

namespace GB\CP\Blog\UnitTests\Commands;

use PHPUnit\Framework\TestCase;
use GB\CP\Blog\Commands\Arguments;
use GB\CP\Blog\Exceptions\ArgumentsException;

class ArgumentsTest extends TestCase
{
  public function testItReturnsArgumentsValueByName(): void
  {
    // Подготовка
    $arguments = new Arguments(['some_key' => 'some_value']);
  
    // Действие
    $value = $arguments->get('some_key');

    // Проверка
    $this->assertEquals('some_value',$value);
    $this->assertIsString($value);
  }

  public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
  {
    // Подготавливаем объект с пустым набором данных
    $arguments = new Arguments([]);

    // Описываем тип ожидаемого исключения
    $this->expectException(ArgumentsException::class);
    // и его сообщение
    $this->expectExceptionMessage("No such argument: some_key");

    // Выполняем действие, приводящее к выбрасыванию исключения
    $arguments->get('some_key');
  }
}