<?php
namespace test\statement;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class ForStatementTest extends TestCase {

  public function testNoExpressions(): void {
    $formula = new Formula(
      'int i = 0;
       for(;;) {i++; break;}
       return i;'
    );
    $this->assertEquals(1, $formula->calculate()->toPHPValue());
  }

  public function testWithExpressions(): void {
    $formula = new Formula(
      'int count = 0;
       for(int i = 0; i < 10; i++) {count++;}
       return count;'
    );
    $this->assertEquals(10, $formula->calculate()->toPHPValue());
  }
}
