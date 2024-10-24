<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\ValueContainer;

/**
 * @author Timo Lehnertz
 *
 *         Represents an empty slot in an array
 */
class ArrayPointerValue extends Value implements ValueContainer {

  private readonly ArrayValue $array;

  private readonly mixed $index;

  public function __construct(ArrayValue $array, mixed $index) {
    $this->array = $array;
    $this->index = $index;
    parent::setContainer($this);
  }

  public function isTruthy(): bool {
    return false;
  }

  public function copy(): Value {
    throw new FormulaRuntimeException('Array key '.$this->index.' does not exist');
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaRuntimeException('Array key '.$this->index.' does not exist');
  }

  public function toPHPValue(): mixed {
    return null;
  }

  public function assign(Value $value): void {
    $this->array->assignKey($this->index, $value);
  }

  public function toString(): string {
    throw new FormulaRuntimeException('Array key '.$this->index.' does not exist');
  }
}
