<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class VoidType extends Type {

  public function __construct() {
    parent::__construct();
  }

  public function getIdentifier(bool $nested = false): string {
    return 'void';
  }

  protected function typeAssignableBy(Type $type): bool {
    return $type instanceof VoidType;
  }

  public function equals(Type $type): bool {
    return $type instanceof VoidType;
  }

  // public function getImplementedOperators(): array {
  //   return [];
  // }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }
}
