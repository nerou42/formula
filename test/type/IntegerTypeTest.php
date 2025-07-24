<?php

namespace test\type;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\FormulaRuntimeException;

class IntegerTypeTest extends TestCase {

  public function testDivisionByZero(): void {
    $formula = new Formula('1 / 0');
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('Division by zero');
    $formula->calculate();
  }

  public function testGetNthBit(): void {
    $formula = new Formula('(0b01010101 & (0b00000001 << 6)) != 0');
    $this->assertEquals(true, $formula->calculate()->toPHPValue());
    $formula = new Formula('(0b01010101 & (0b00000001 << 7)) != 0');
    $this->assertEquals(false, $formula->calculate()->toPHPValue());
  }
}
