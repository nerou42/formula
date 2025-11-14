<?php
declare(strict_types=1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaException;
use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;

/**
 * Represents an operators that can be implemented by values
 * @author Timo Lehnertz
 */
class ImplementableOperator implements FormulaPart {

  public const TYPE_ADDITION = 0;
  public const TYPE_SUBTRACTION = 1;
  public const TYPE_UNARY_PLUS = 2;
  public const TYPE_UNARY_MINUS = 3;
  public const TYPE_MULTIPLICATION = 4;
  public const TYPE_DIVISION = 5;
  public const TYPE_MODULO = 6;
  public const TYPE_EQUALS = 7;
  public const TYPE_GREATER = 8;
  public const TYPE_LESS = 9;
  public const TYPE_LOGICAL_AND = 10;
  public const TYPE_LOGICAL_OR = 11;
  public const TYPE_DIRECT_ASSIGNMENT = 12;
  public const TYPE_DIRECT_ASSIGNMENT_OLD_VAL = 13;
  public const TYPE_MEMBER_ACCESS = 14; // a.b
  public const TYPE_SCOPE_RESOLUTION = 15; // ::
  public const TYPE_LOGICAL_NOT = 16;
  public const TYPE_LOGICAL_XOR = 17;
  public const TYPE_INSTANCEOF = 18;
  public const TYPE_NEW = 19;
  public const TYPE_ARRAY_ACCESS = 20;
  public const TYPE_CALL = 21;
  public const TYPE_TYPE_CAST = 22;
  public const TYPE_BITWISE_AND = 23;
  public const TYPE_BITWISE_OR = 24;
  public const TYPE_LEFT_SHIFT = 25;
  public const TYPE_RIGHT_SHIFT = 26;
  public const MAX_ID = self::TYPE_TYPE_CAST;

  /**
   * @psalm-var ImplementableOperator::TYPE_*
   */
  private readonly int $id;

  private readonly OperatorType $operatorType;

  private readonly string $identifier;

  /**
   * @psalm-param ImplementableOperator::TYPE_* $id
   */
  public function __construct(int $id) {
    $this->id = $id;
    $this->operatorType = self::idToOperatorType($id);
    $this->identifier = self::idToIdentifier($id);
  }

  private static function idToOperatorType(int $id): OperatorType {
    return match($id) {
      ImplementableOperator::TYPE_ADDITION,
      ImplementableOperator::TYPE_SUBTRACTION,
      ImplementableOperator::TYPE_MULTIPLICATION,
      ImplementableOperator::TYPE_DIVISION,
      ImplementableOperator::TYPE_MODULO,
      ImplementableOperator::TYPE_EQUALS,
      ImplementableOperator::TYPE_GREATER,
      ImplementableOperator::TYPE_LESS,
      ImplementableOperator::TYPE_LOGICAL_AND,
      ImplementableOperator::TYPE_DIRECT_ASSIGNMENT,
      ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL,
      ImplementableOperator::TYPE_MEMBER_ACCESS,
      ImplementableOperator::TYPE_SCOPE_RESOLUTION,
      ImplementableOperator::TYPE_LOGICAL_XOR,
      ImplementableOperator::TYPE_INSTANCEOF,
      ImplementableOperator::TYPE_LOGICAL_OR,
      ImplementableOperator::TYPE_CALL,
      ImplementableOperator::TYPE_TYPE_CAST,
      ImplementableOperator::TYPE_ARRAY_ACCESS,
      ImplementableOperator::TYPE_BITWISE_AND,
      ImplementableOperator::TYPE_BITWISE_OR,
      ImplementableOperator::TYPE_LEFT_SHIFT,
      ImplementableOperator::TYPE_RIGHT_SHIFT => OperatorType::InfixOperator,
      ImplementableOperator::TYPE_NEW,
      ImplementableOperator::TYPE_UNARY_PLUS,
      ImplementableOperator::TYPE_UNARY_MINUS,
      ImplementableOperator::TYPE_LOGICAL_NOT => OperatorType::PrefixOperator,
      default => throw new FormulaException('Invalid ImplementableOperator ID ' . $id)
    };
  }

  private static function idToIdentifier(int $id): string {
    return match($id) {
      ImplementableOperator::TYPE_SCOPE_RESOLUTION => '::',
      ImplementableOperator::TYPE_MEMBER_ACCESS => '.',
      ImplementableOperator::TYPE_UNARY_PLUS => '+',
      ImplementableOperator::TYPE_UNARY_MINUS => '-',
      ImplementableOperator::TYPE_LOGICAL_NOT => '!',
      ImplementableOperator::TYPE_NEW => 'new',
      ImplementableOperator::TYPE_INSTANCEOF => 'instanceof',
      ImplementableOperator::TYPE_MULTIPLICATION => '*',
      ImplementableOperator::TYPE_DIVISION => '/',
      ImplementableOperator::TYPE_MODULO => '%',
      ImplementableOperator::TYPE_ADDITION => '+',
      ImplementableOperator::TYPE_SUBTRACTION => '-',
      ImplementableOperator::TYPE_GREATER => '>',
      ImplementableOperator::TYPE_LESS => '<',
      ImplementableOperator::TYPE_EQUALS => '==',
      ImplementableOperator::TYPE_LOGICAL_AND => '&&',
      ImplementableOperator::TYPE_LOGICAL_OR => '||',
      ImplementableOperator::TYPE_LOGICAL_XOR => '^',
      ImplementableOperator::TYPE_DIRECT_ASSIGNMENT => '=',
      ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL => '=',
      ImplementableOperator::TYPE_TYPE_CAST => 'typecast',
      ImplementableOperator::TYPE_ARRAY_ACCESS => '[]',
      ImplementableOperator::TYPE_CALL => '()',
      ImplementableOperator::TYPE_BITWISE_AND => '&',
      ImplementableOperator::TYPE_BITWISE_OR => '|',
      ImplementableOperator::TYPE_LEFT_SHIFT => '<<',
      ImplementableOperator::TYPE_RIGHT_SHIFT => '>>',
      default => throw new FormulaException('Invalid ImplementableOperator ID ' . $id)
    };
  }

  public function getOperatorType(): OperatorType {
    return $this->operatorType;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }

  public function getID(): int {
    return $this->id;
  }
}
