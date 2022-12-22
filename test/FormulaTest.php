<?php

namespace test;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\ParsingException;
use DateTime;

class FormulaTest extends TestCase {
  
  protected function setUp(): void {
    parent::setUp();
  }
  
  public function testVariables(): void {
    $str = 'a+b+c+d+e';
    $formula = new Formula($str);
    for ($i = 0; $i < 10; $i++) {
      $a = rand(-1000, 1000);
      $b = rand(-1000, 1000);
      $c = rand(-1000, 1000);
      $d = rand(-1000, 1000);
      $e = rand(-1000, 1000);
      $formula->setVariable('a', $a);
      $formula->setVariable('b', $b);
      $formula->setVariable('c', $c);
      $formula->setVariable('d', $d);
      $formula->setVariable('e', $e);
      $this->assertEquals($a + $b + $c + $d + $e, $formula->calculate());
    }
  }
  
  public function testpow(): void {
    $str = 'pow(a,b)';
    $formula = new Formula($str);
    for ($i = 0; $i < 1; $i++) {
      $a = rand(0, 10);
      $b = rand(0, 10);
      $formula->setVariable('a', $a);
      $formula->setVariable('b', $b);
      $result = $formula->calculate();
      $this->assertEquals(round(pow($a, $b)), round($result));
    }
  }
  
  public function testMathRules(): void {
    $str = '(a+(b-c))*(a/d)*e+pow(a,b)*(b/d)-pow(a,e)';
    $formula = new Formula($str);
    for ($i = 0; $i < 10; $i++) { // tested with 1000000
      $a = rand(1, 10);
      $b = rand(-10, 10);
      $c = rand(-10, 10);
      $d = rand(1, 10);
      $e = rand(1, 10);
      $f = rand(-10, 10);
      $formula->setVariable('a', $a);
      $formula->setVariable('b', $b);
      $formula->setVariable('c', $c);
      $formula->setVariable('d', $d);
      $formula->setVariable('e', $e);
      $formula->setVariable('f', $f);
      $correct = round(($a+($b-$c))*($a/$d)*$e+pow($a,$b)*($b/$d)-pow($a,$e));
      $calculated = $formula->calculate();
      $this->assertTrue(abs($calculated - $correct) < 1); // rounding errors...
    }
  }
  
  public function testFunctions(): void {
    $str = 'max(min(a,b),c)';
    $formula = new Formula($str);
    for ($i = 0; $i < 10; $i++) {
      $a = rand(-1000, 1000);
      $b = rand(-1000, 1000);
      $c = rand(-1000, 1000);
      $formula->setVariable('a', $a);
      $formula->setVariable('b', $b);
      $formula->setVariable('c', $c);
      $this->assertEquals(max(min($a, $b), $c), $formula->calculate());
    }
  }
  
  public function testNesting(): void {
    $str = '((min(a,2)*b+5)/(((2+5)-5)+99*0))-7.5';
    $formula = new Formula($str);
    $formula->setVariable('a', 2);
    $formula->setVariable('b', 5);
    $result = $formula->calculate();
    $this->assertEquals($result, 0);
  }
  
  /**
   * Dateintervals are not 100% precise
   */
  public function testDates(): void {
    $date = new DateTime(); // now
    $str = '"'.$date->format(DateTime::ISO8601).'" + "P5M" - "P1M" + "P1M" - "P5M"';
    $formula = new Formula($str);
    $result = $formula->calculate();
    $this->assertEquals($date->getTimestamp(), $result);
    
    $date = new DateTime(); // now
    $str = '"'.$date->format(DateTime::ISO8601).'" + 10 * "P1M" - 10 * "P1M"';
    $formula = new Formula($str);
    $result = $formula->calculate();
    $resDate = new DateTime();
    $resDate->setTimestamp($result);
    $this->assertEquals($date->getTimestamp(), intval($result));
    
    $date = new DateTime(); // now
    $str = '"'.$date->format(DateTime::ISO8601).'" - "'.$date->format(DateTime::ISO8601).'"';
    $formula = new Formula($str);
    $result = $formula->calculate();
    $this->assertEquals($result, 0);
  }
  
  public function testEmptyBrackets(): void {
    $str = '(1*((((())))1)2)';
    $formula = new Formula($str);
    $res = $formula->calculate();
    $this->assertEquals($res, 0);
  }
  
  public function testNotClosedBracket(): void {
    $this->expectException(ExpressionNotFoundException::class);
    $this->expectExceptionMessage('Unexpected end of input');
    new Formula('(1*5');
  }
  
  public function testNotAssignedVariable(): void {
    $formula = new Formula('(1*a)');
    $this->expectException(ExpressionNotFoundException::class);
    $this->expectExceptionMessage("Can't calculate. Variable a has no value");
    $formula->calculate();
  }
  
  public function testNotAssignedMethod(): void {
    $formula = new Formula('(1*a())');
    $this->expectException(ExpressionNotFoundException::class);
    $this->expectExceptionMessage('No method provided for a!');
    $formula->calculate();
  }
  
  public function booleanDataProvider() {
    return [
      [false, false],
      [false, true],
      [true, false],
      [true, true]
    ];
  }
  
  /**
   * @dataProvider booleanDataProvider
   */
  public function testLogicalOperators($a, $b): void {
    $aStr = $a ? "true" : "false";
    $bStr = $b ? "true" : "false";
    $formula = new Formula("$aStr&&$bStr");
    $this->assertEquals($a && $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr||$bStr");
    $this->assertEquals($a || $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr==$bStr");
    $this->assertEquals($a == $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr!=$bStr");
    $this->assertEquals($a != $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr^$bStr");
    $this->assertEquals($a ^ $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr<$bStr");
    $this->assertEquals($a < $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr>$bStr");
    $this->assertEquals($a > $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr<=$bStr");
    $this->assertEquals($a <= $b, $formula->calculate() == 0 ? false : true);
    
    $formula = new Formula("$aStr>=$bStr");
    $this->assertEquals($a >= $b, $formula->calculate() == 0 ? false : true);

    $formula = new Formula("!$aStr");
    $this->assertEquals(!$a, $formula->calculate() == 0 ? false : true);
  }
  
  public function ternaryDataProvider() : array {
    $arr = [];
    for ($i = 0; $i < 1; $i++) {
      $arr []= [rand(-1, -1), rand(-1, -1) ,rand(-1, -1)];
//       $arr []= [rand(-100, 100), rand(-100, 100) ,rand(-100, 100)];
    }
    return $arr;
}
  
  /**
   * @dataProvider ternaryDataProvider
   */
  public function testTernary($a, $b, $c): void {
    $formula = new Formula("$a < 0 ? $b : $c");
    $this->assertEquals($formula->calculate(), $a < 0 ? $b : $c);
    $formula = new Formula("max($b, min($a < 0 ? $b : $c, $c))");
    $this->assertEquals($formula->calculate(), max($b, min($a < 0 ? $b : $c, $c)));
//  testing Not operator
    $formula = new Formula("max($b, min(!($a < 0) ? $b : $c, $c))");
    $this->assertEquals($formula->calculate(), max($b, min(!($a < 0) ? $b : $c, $c)));
  }
  
  public function testTernaryError(): void {
    $this->expectException(ExpressionNotFoundException::class);
    $this->expectExceptionMessage('Expected ":" (Ternary). Formula: "max(1,min(2<0?34,5))"  At position: 11');
    $formula = new Formula('max(1, min(2 < 0 ? 3  4, 5))');
    $formula->calculate();
  }
  
  public function numberProvider(): array {
    return [
      [-1],[5],[20],[-6],[0]
    ];
  }
  
  /**
   * @dataProvider numberProvider
   */
  public function testBuildInFuncs($a): void {
    $formula = new Formula('min(2a, 0)');
    $formula->setVariable('a', $a);
    $this->assertEquals($formula->calculate(), min(2*$a, 0));
    
    $formula = new Formula('max(2a, 0)');
    $formula->setVariable('a', $a);
    $this->assertEquals($formula->calculate(), max(2*$a, 0));
    
    $formula = new Formula('sqrt(10+a)');
    $formula->setVariable('a', $a);
    $this->assertEquals(round($formula->calculate()), round(sqrt(10+$a)));
    
    $formula = new Formula('pow(a, a)');
    $formula->setVariable('a', $a);
    $this->assertEquals(round($formula->calculate()), round(pow($a, $a)));
    
    $formula = new Formula('floor(a * 0.3)');
    $formula->setVariable('a', $a);
    $this->assertEquals($formula->calculate(), floor($a * 0.3));
    
    $formula = new Formula('ceil(a * 0.3)');
    $formula->setVariable('a', $a);
    $this->assertEquals($formula->calculate(), ceil($a * 0.3));
    
    $formula = new Formula('round(a * 0.3)');
    $formula->setVariable('a', $a);
    $this->assertEquals($formula->calculate(), round($a * 0.3));

    $formula = new Formula('sin(a)');
    $formula->setVariable('a', $a);
    $this->assertEquals(round($formula->calculate()), round(sin($a)));
    
    $formula = new Formula('cos(a)');
    $formula->setVariable('a', $a);
    $this->assertEquals(round($formula->calculate()), round(cos($a)));
    
    $formula = new Formula('tan(a)');
    $formula->setVariable('a', $a);
    $this->assertEquals(round($formula->calculate()), round(tan($a)));
    
    $formula = new Formula('tan(a)');
    $formula->setVariable('a', $a);
    $this->assertEquals(round($formula->calculate()), round(tan($a)));

    $formula = new Formula('abs(a)');
    $formula->setVariable('a', $a);
    $this->assertEquals(round($formula->calculate()), abs($a));
    
    $formula = new Formula('asVector(1,2,3,4)[2]');
    $this->assertEquals(3, $formula->calculate());
    
    $formula = new Formula('sizeof({1,2,3,4})');
    $this->assertEquals(4, $formula->calculate());
  }
  
  // from original repo at https://github.com/socialist/formula
  public function testAllResults() {
    $parser = new Formula('2 * 2.65');
    $this->assertEquals('5.3', $parser->calculate());
    
    $parser = new Formula('2 * 2.65 + 25');
    $this->assertEquals('30.3', $parser->calculate());
    
    $parser = new Formula('2 * 2.65 + 25 / 3');
    $this->assertEquals(round('13.63'), round($parser->calculate()));
    
    $parser = new Formula('2 + 3 * 2.65 + 25');
    $this->assertEquals('34.95', $parser->calculate());
    
    $parser = new Formula('2 + 3 * 2.65 + 25 - 26');
    $this->assertEquals('8.95', $parser->calculate());
    
    $parser = new Formula('2 + 3 - 4 * 2.65 + 25 - 26');
    $this->assertEquals('-6.6', $parser->calculate());
    
    $parser = new Formula('( 15 + p ) * 2.65');
    $parser->setVariable('p', 235);
    $this->assertEquals('662.5', $parser->calculate());
    
    $parser = new Formula('( 2 + ( 3 - 4 ) ) * 2.65 + 25 - 26');
    $this->assertEquals('1.65', $parser->calculate());
    
    $parser = new Formula('( 2 + ( 3 - 4 ) ) * ( 2.65 + ( 25 - 26 ) )');
    $this->assertEquals('1.65', $parser->calculate());
    
    $parser = new Formula('( p + ( 3 * 235 - 4 ) ) + 25');
    $parser->setVariable('p', 2);
    $this->assertEquals('728', $parser->calculate());
  }
  
  public function testGetVariables() {
    $formula = new Formula('a + b + max(c, b ? d : e)');
    $this->assertEquals($formula->getVariables(), ['a', 'b', 'c', 'd', 'e']);
  }
  
  public function testVectors(): void {
  	$formula = new Formula('{1,2,3} + {1,2,3}');
  	$this->assertEquals($formula->calculate(), [2,4,6]);
  	$formula = new Formula('{1,2,3} + 5');
  	$this->assertEquals($formula->calculate(), [6,7,8]);
  	
  	$formula = new Formula('{1,2,3} - {1,2,3}');
  	$this->assertEquals($formula->calculate(), [0,0,0]);
  	$formula = new Formula('{1,2,3} - 5');
  	$this->assertEquals($formula->calculate(), [-4,-3,-2]);
  	
  	$formula = new Formula('{1,2,3} * {1,2,3}');
  	$this->assertEquals($formula->calculate(), [1,4,9]);
  	$formula = new Formula('{1,2,3} * 5');
  	$this->assertEquals($formula->calculate(), [5,10,15]);
  	
  	$formula = new Formula('{1,2,3} / {1,2,3}');
  	$this->assertEquals($formula->calculate(), [1,1,1]);
  	$formula = new Formula('{10,15,20} / 5');
  	$this->assertEquals($formula->calculate(), [2,3,4]);
  	
  	$formula = new Formula('max({-10,15,20})');
  	$this->assertEquals(20, $formula->calculate());
  	
  	$formula = new Formula('min({-10,15,20})');
  	$this->assertEquals(-10, $formula->calculate());
  }
  
  public function testVectorsOffsets(): void {
    $formula = new Formula('{1,2,3}[0]');
    $this->assertEquals($formula->calculate(), 1);
    
    $formula = new Formula('{1,2,3}[max(a,b)]');
    $formula->setVariable('a', 2);
    $formula->setVariable('b', -1);
    $this->assertEquals(3, $formula->calculate());
  }
  
  public function testVectorInvalidIndex(): void {
    $this->expectException(ExpressionNotFoundException::class);
    $this->expectExceptionMessage('123 Is no valid array index');
    $formula = new Formula('{1,2,3}["123"]');
    $formula->calculate();
  }
  
  public function testVectoroutOfBounds1(): void {
    $this->expectException(\OutOfBoundsException::class);
    $this->expectExceptionMessage('3 not in range 0 - 3');
    $formula = new Formula('{1,2,3}[3]');
    $formula->calculate();
  }
  
  public function testVectoroutOfBounds2(): void {
    $this->expectException(\OutOfBoundsException::class);
    $this->expectExceptionMessage('-1 not in range 0 - 3');
    $formula = new Formula('{1,2,3}[-1]');
    $formula->calculate();
  }
  
  public function testUnexpectedEndOfInputException(): void {
    $this->expectException(ExpressionNotFoundException::class);
    $this->expectExceptionMessage('Unexpected end of input. Formula: "(1+2+3"  At position: 6');
    $formula = new Formula('(1+2+3');
    $formula->calculate();
  }

  public function strFunc() {
    return "Hallo welt";
  }
  
  public function testGetStringLiterals(): void {
    $formula = new Formula('strFunc("hallo", "welt", "hallo", "welt")');
    $formula->setMethod('strFunc', [$this, "strFunc"]);
    $this->assertEquals("Hallo welt", $formula->calculate());
    $this->assertEquals(['hallo', 'welt', 'hallo', 'welt'], $formula->getStringLiterals());
  }
  
  public function testRenameVariables(): void {
    $formula = new Formula('a+b+max(a,min(a,b))');
    $formula->renameVariables('a', 'c');
    $formula->renameVariables('b', 'd');
    $formula->setVariable('c', 10);
    $formula->setVariable('d', 20);
    $this->assertEquals(10+20+max(10,min(10, 20)), $formula->calculate());
  }
  
  public function testRenameStrings(): void {
    $formula = new Formula('"Hallo"');
    $formula->renameStrings('Hallo', 'Welt');
    $this->assertEquals('Welt', $formula->calculate());
  }
  
  public function testRenameMethods(): void {
    $formula = new Formula('abc(1,2)');
    $formula->renameMethods('abc', 'min');
    $this->assertEquals(1, $formula->calculate());
  }
}