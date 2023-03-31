<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\expression\Expression;
use InvalidArgumentException;

/**
 *
 * @author Timo Lehnertz
 *
 */
abstract class Operator implements SubFormula {

  /**
   * Prioriry of this operator over other operators
   * @readonly
   */
  private int $priority;
  
  /**
   * Can left and right be intervhanged
   * @readonly
   */
  private bool $commutative;
  
  /**
   * Is lefthand expression needed
   * @readonly
   */
  private bool $needsLeft;
  
  /**
   * Is righthand expression needed
   * @readonly
   */
  private bool $needsRight;
  
  /**
   * Will use lefthand expression
   * @readonly
   */
  private bool $usesLeft;
  
  /**
   * Will use righthand expression
   * @readonly
   */
  private bool $usesRight;
  
  private ?string $stringRepresentation;
  
  public function __construct(?string $stringRepresentation, int $priority, bool $commutative, bool $needsLeft = true, bool $needsRight = true, bool $usesLeft = true, bool $usesRight = true) {
    $this->priority = $priority;
    $this->commutative = $commutative;
    $this->needsLeft = $needsLeft;
    $this->needsRight = $needsRight;
    $this->usesLeft = $usesLeft;
    $this->usesRight = $usesRight;
    $this->stringRepresentation = $stringRepresentation;
  }
  
  public function getPriority(): int {
    return $this->priority;
  }
  
  public function needsLeft(): bool {
    return $this->needsLeft;
  }
  
  public function needsRight(): bool {
    return $this->needsRight;
  }
  
  public function usesLeft(): bool {
    return $this->usesLeft;
  }
  
  public function usesRight(): bool {
    return $this->usesRight;
  }
  
  /**
   * @throws InvalidArgumentException
   */
  public function calculate(Expression $left, Expression $right): Calculateable {
    try {
      return $this->doCalculate($left->calculate(), $right->calculate());
    } catch(InvalidArgumentException $e) {
      if($this->commutative) { // try other direction
        return $this->doCalculate($right->calculate(), $left->calculate());
      } else {
        throw $e;
      }
    }
  }
  
  public static function fromString(string $name): Operator {
    switch($name) {
      case "+":   return new Increment();
      case "-":   return new Subtraction();
      case "*":   return new Multiplication();
      case "/":   return new Division();
      case "^":   return new XorOperator();
      case "&&":  return new AndOperator();
      case "||":  return new OrOperator();
      case "!=":  return new NotEqualsOperator();
      case "!":   return new NotOperator();
      case "==":  return new EqualsOperator();
      case "<":  return new SmallerOperator();
      case ">":  return new GreaterOperator();
      case "<=":  return new SmallerEqualsOperator();
      case "<":   return new SmallerOperator();
      case ">=":  return new GreaterEqualsOperator();
      case "<":   return new GreaterOperator();
      default: throw new \Exception("Invalid operator: $name"); // shouldnt happen as this gets sorted out in tokenizer stage
    }
  }

  public abstract function doCalculate(Calculateable $left, Calculateable $right): Calculateable;
  
  public function toString(): string {
    return $this->stringRepresentation;
  }
}