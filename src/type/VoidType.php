<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class VoidType implements Type {

  public function getIdentifier(bool $nested = false): string {
    return 'void';
  }

  public function canCastTo(Type $type): bool {
    return $type instanceof VoidType;
  }

  public function getSubProperties(): array {
    return [];
  }

  public function getImplementedOperators(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }
}
