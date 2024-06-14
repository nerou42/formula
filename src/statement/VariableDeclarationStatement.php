<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\expression\TypeExpression;
use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class VariableDeclarationStatement implements Statement {

  private Type $type;

  private readonly string $identifier;

  private Expression $initilizer;

  public function __construct(Type $type, string $identifier, Expression $initilizer) {
    $this->type = $type;
    $this->identifier = $identifier;
    $this->initilizer = $initilizer;
  }

  public function validate(Scope $scope): StatementReturnType {
    $initilizerType = $this->initilizer->validate($scope);
    if(!$initilizerType->equals($this->type)) {
      $this->initilizer = new OperatorExpression($this->initilizer, new TypeCastOperator(false, $this->type), new TypeExpression($this->type));
      $this->initilizer->validate($scope);
    }
    $scope->define($this->identifier, $this->type);
    return new StatementReturnType($this->type, false, false);
  }

  public function run(Scope $scope): StatementReturn {
    $value = $this->initilizer->run($scope);
    $scope->define($this->identifier, $this->type, $value);
    return new StatementReturn($value, false, false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->type->getIdentifier().' '.$this->identifier.' = '.$this->initilizer->toString($prettyPrintOptions).';';
  }
}
