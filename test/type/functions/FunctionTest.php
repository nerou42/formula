<?php
namespace test\type\functions;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\Value;

class FunctionTest extends TestCase {

  public function func1(): int {
    return 0;
  }

  public function func2(): int {
    return 0;
  }

  public function testTruthy(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'func1', $this->func1(...));
    $formula = new Formula('func1 || false', $scope);
    $this->assertEquals(true, $formula->calculate()->toPHPValue());
  }

  public function testEquals(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'func1', $this->func1(...));
    $scope->definePHP(true, 'func2', $this->func2(...));
    $formula = new Formula('func1 == func1', $scope);
    $this->assertEquals(true, $formula->calculate()->toPHPValue());
    $formula = new Formula('func1 == func2', $scope);
    $this->assertEquals(false, $formula->calculate()->toPHPValue());
  }

  //   public function testToPHPValue(): void {
  //     $scope = new Scope();
  //     $scope->definePHP(true, 'func1', [$this,'func1']);
  //     $formula = new Formula('func1', $scope);
  //     $this->expectException(FormulaBugException::class);
  //     $this->expectExceptionMessage('FunctionValue list does not have a php representation');
  //     $formula->calculate()->toPHPValue();
  //   }
  public function testToString(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'func1', $this->func1(...));
    $formula = new Formula('"string "+func1', $scope);
    $this->assertEquals('string function', $formula->calculate()->toPHPValue());
  }

  public function passthrough(Value $input): Value {
    return $input;
  }

  public function testPassthrough(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'passthrough', $this->passthrough(...));
    $functionType = $scope->use('passthrough');
    if($functionType instanceof FunctionType) {
      $this->assertInstanceOf(MixedType::class, $functionType->arguments->getArgumentType(0));
    } else {
      $this->fail('expected function type');
    }
    $formula = new Formula('passthrough("abc")', $scope);
    $this->assertInstanceOf(MixedType::class, $formula->getReturnType());
    $this->assertEquals('abc', $formula->calculate()->toPHPValue());
  }
}
