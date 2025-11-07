<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidValue;

/**
 * @author Timo Lehnertz
 */
class PHPFunctionBody implements FunctionBody {

  /**
   * @var callable(mixed...):mixed
   */
  private readonly mixed $callable;

  private readonly RuntimeFunctionArgsData $runtimeData;

  /**
   * PHP void Functions always return null
   */
  private readonly bool $voidFunction;

  /**
   * @param callable(mixed...):mixed $callable
   */
  public function __construct(callable $callable, bool $voidFunction, RuntimeFunctionArgsData $runtimeData) {
    $this->callable = $callable;
    $this->voidFunction = $voidFunction;
    $this->runtimeData = $runtimeData;
  }

  public function call(OuterFunctionArgumentListValue $args): Value {
    $argList = [];
    for($i = 0;$i < count($args->getValues());$i++) {
      /** @var Value $argValue */
      $argValue = $args->getValues()[$i];
      if($this->runtimeData->argAcceptsValue($i)) {
        $argList[$i] = $argValue;
      } else {
        $argList[$i] = $argValue->toPHPValue();
      }
    }
    $phpReturn = call_user_func_array($this->callable, $argList);
    if(!$this->voidFunction) {
      return Scope::convertPHPVar($phpReturn, true)[1];
    } else {
      return new VoidValue();
    }
  }
}
