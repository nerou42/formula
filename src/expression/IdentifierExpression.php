<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use SebastianBergmann\Type\VoidType;

/**
 * @author Timo Lehnertz
 */
class IdentifierExpression implements Expression {

  private readonly string $identifier;

  private Scope $scope;

  public function __construct(string $identifier) {
    $this->identifier = $identifier;
  }

  public function run(): Value {
    return $this->identifier;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }

  public function getSubParts(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    $this->scope = $scope;
    return new VoidType();
  }
}
