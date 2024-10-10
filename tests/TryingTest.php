<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DataProvider;


class TryingTest extends TestCase {
  
  public function testEmpty(): array {
      $stack = [];
      $this->assertEmpty($stack);

      return $stack;
  }

  #[Depends('testEmpty')]
  public function testPush(array $stack): array {
      $stack[] = 'foo';
      $this->assertSame('foo', $stack[count($stack) - 1]);
      $this->assertNotEmpty($stack);

      return $stack;
  }

  #[Depends('testPush')]
  public function testPop(array $stack): void {
      $this->assertSame('foo', array_pop($stack));
      $this->assertEmpty($stack);
  }


  public static function additionProvider(): array {
      return [
          [0, 0, 0],
          [0, 1, 1],
          [1, 0, 1],
          [1, 1, 2],
      ];
  }

  #[DataProvider('additionProvider')]
  public function testAdd(int $a, int $b, int $expected): void {
      $this->assertSame($expected, $a + $b);
  }
}