<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use const false;

/**
 * @author Timo Lehnertz
 */
class EnumInstanceType extends Type {

  private readonly EnumTypeType $enumType;

  public function __construct(EnumTypeType $enumType) {
    $this->enumType = $enumType;
  }

  protected function typeAssignableBy(Type $type): bool {
    return $type instanceof EnumInstanceType && $this->enumType->equals($type->enumType);
  }

  public function equals(Type $type): bool {
    return $type instanceof EnumInstanceType && $this->enumType->equals($type->enumType);
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function getIdentifier(bool $isNested = false): string {
    return 'enumInstance('.$this->enumType->getIdentifier().')';
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('EnumTypeType');
  }
}