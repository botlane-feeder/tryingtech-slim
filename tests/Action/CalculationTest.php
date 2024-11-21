<?php

namespace Test\Action;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DataProvider;

use App\Action\Calculation;

class CalculationTest extends TestCase {

  #[DataProvider('stepsProvider')]
  public function testGetYFromSteps(array $thresholds, array $values, int|float $myX, int|float $yExpected) {
    $this->assertEquals( Calculation::getYFromSteps($thresholds, $values, $myX), $yExpected);
  }

  public static function stepsProvider(): array {
    return [
        [ [2,5,8], [1,2,3], 1, 1],
        [ [2,5,8], [1,2,3], 5, 2],
        [ [2,5,8], [1,2,3], 9, 3],

        [ [2,5,8], [1,2,3], 3.5, 1.5],
        [ [2,5,8], [1,2,3], 7, 2.66],
      ];
}

}