<?php
declare(strict_types=1);

namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\NeverType;
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;
use TimoLehnertz\formula\ExitIfNullException;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\functions\SpecificReturnType;
use TimoLehnertz\formula\type\IntegerType;

/**
 * @author Timo Lehnertz
 */
class DefaultScope extends Scope {

  public function __construct() {
    $intOrFloat = CompoundType::buildFromTypes([new FloatType(), new IntegerType()]);
    $numberVargType = new OuterFunctionArgumentListType([new OuterFunctionArgument(CompoundType::buildFromTypes([new FloatType(), new IntegerType(), new ArrayType(new MixedType(), $intOrFloat)]), true, true)], true);
    $this->definePHP(true, 'print', self::printFunc(...));
    $this->definePHP(true, 'println', self::printlnFunc(...));
    $this->definePHP(true, 'pow', self::powFunc(...));
    $this->definePHP(true, "min", self::minFunc(...), $numberVargType);
    $this->definePHP(true, "max", self::maxFunc(...), $numberVargType);
    $this->definePHP(true, "sqrt", self::sqrtFunc(...));
    $this->definePHP(true, "ceil", self::ceilFunc(...));
    $this->definePHP(true, "floor", self::floorFunc(...));
    $this->definePHP(true, "round", self::roundFunc(...));
    $this->definePHP(true, "sin", self::sinFunc(...));
    $this->definePHP(true, "cos", self::cosFunc(...));
    $this->definePHP(true, "tan", self::tanFunc(...));
    $this->definePHP(true, "is_nan", self::is_nanFunc(...));
    $this->definePHP(true, "abs", self::absFunc(...));
    $this->definePHP(true, "asVector", self::asVectorFunc(...));
    $this->definePHP(true, "sizeof", self::sizeofFunc(...));
    $this->definePHP(true, "inRange", self::inRangeFunc(...));
    $this->definePHP(true, "reduce", self::reduceFunc(...), null, null, new SpecificReturnType('FORMULA_REDUCE', static fn(OuterFunctionArgumentListType $args): ?Type => $args->getArgumentType(0)));
    $this->definePHP(true, "firstOrNull", self::firstOrNullFunc(...), null, null, new SpecificReturnType('FORMULA_FIRST_OR_NULL', static function (OuterFunctionArgumentListType $args): ?Type {
      $type = $args->getArgumentType(0);
      if ($type instanceof ArrayType) {
        if ($type->getElementsType() instanceof NeverType) {
          return new NullType();
        }
        return CompoundType::buildFromTypes([new NullType(), $type->getElementsType()]);
      }
      return null;
    }));
    $this->definePHP(true, "lastOrNull", self::lastOrNullFunc(...), null, null, new SpecificReturnType('FORMULA_FIRST_OR_NULL', function (OuterFunctionArgumentListType $args): ?Type {
      $type = $args->getArgumentType(0);
      if ($type instanceof ArrayType) {
        if ($type->getElementsType() instanceof NeverType) {
          return new NullType();
        }
        return CompoundType::buildFromTypes([new NullType(), $type->getElementsType()]);
      }
      return null;
    }));
    $this->definePHP(true, "assertTrue", self::assertTrueFunc(...));
    $this->definePHP(true, "assertFalse", self::assertFalseFunc(...));
    $this->definePHP(true, "assertEquals", self::assertEqualsFunc(...));
    
    $this->definePHP(true, "sum", self::sumFunc(...), $numberVargType);
    $this->definePHP(true, "avg", self::avgFunc(...), $numberVargType);
    $callbackType = new FunctionType(new OuterFunctionArgumentListType([new OuterFunctionArgument(new MixedType())]), new BooleanType());
    $this->definePHP(true, "array_filter", self::array_filterFunc(...), ['callback' => $callbackType], null, 
        new SpecificReturnType('FORMULA_ARRAY_FILTER', static fn(OuterFunctionArgumentListType $args): ?Type => $args->getArgumentType(0)));

    $this->definePHP(true, "earlyReturnIfNull", self::earlyReturnIfNullFunc(...), null, null, new SpecificReturnType('FORMULA_EARLY_RETURN_IF_NULL', static function (OuterFunctionArgumentListType $args): ?Type {
      $type = $args->getArgumentType(0);
      if ($type instanceof CompoundType) {
        return $type->eliminateType(new NullType());
      } else if ($type instanceof NullType) {
        return new NeverType();
      } else {
        return $type;
      }
    }));
    // constants
    $this->definePHP(true, "PI", M_PI);
  }

  public static function earlyReturnIfNullFunc(mixed $value): mixed {
    if ($value === null) {
      throw new ExitIfNullException();
    } else {
      return $value;
    }
  }

  public static function array_filterFunc(array $array, callable $callback): array {
    return array_filter($array, $callback);
  }

  public static function printFunc(string $str): void {
    echo $str;
  }

  public static function printlnFunc(string $str): void {
    self::printFunc($str . PHP_EOL);
  }

  private static function mergeArraysRecursive(array $arrays): array {
    $merged = [];
    foreach ($arrays as $val) {
      if (is_array($val)) {
        $merged = array_merge($merged, DefaultScope::mergeArraysRecursive($val));
      } else {
        $merged[] = $val;
      }
    }
    return $merged;
  }

  public static function minFunc(float|array ...$values): float {
    $values = DefaultScope::mergeArraysRecursive($values);
    if (count($values) === 0) {
      return 0;
    }
    return min($values);
  }

  public static function maxFunc(float|array ...$values): float {
    $values = DefaultScope::mergeArraysRecursive($values);
    if (count($values) === 0) {
      return 0;
    }
    return max($values);
  }

  public static function powFunc(float $base, float $exp): float {
    return $base ** $exp;
  }

  public static function sqrtFunc(float $arg): float {
    return sqrt($arg);
  }

  public static function ceilFunc(int|float $value): float {
    return ceil($value);
  }

  public static function floorFunc(int|float $value): float {
    return floor($value);
  }

  public static function roundFunc(int|float $num, int $precision = 0): float {
    return round($num, $precision);
  }

  public static function sinFunc(int|float $arg): float {
    return sin($arg);
  }

  public static function cosFunc(int|float $arg): float {
    return cos($arg);
  }

  public static function tanFunc(int|float $arg): float {
    return tan($arg);
  }

  public static function is_nanFunc(int|float $val): bool {
    return is_nan($val);
  }

  public static function absFunc(int|float $number): float {
    return abs($number);
  }

  public static function asVectorFunc(mixed ...$values): array {
    return $values;
  }

  public static function sizeofFunc(mixed ...$values): int {
    return count(DefaultScope::mergeArraysRecursive($values));
  }

  public static function inRangeFunc(float|int $value, float|int $min, float|int $max): bool {
    return ($min <= $value) && ($value <= $max);
  }

  public static function reduceFunc(array $values, array $filter): array {
    $result = [];
    foreach ($values as $value) {
      if (in_array($value, $filter)) {
        $result[] = $value;
      }
    }
    return $result;
  }

  public static function firstOrNullFunc(array $array): mixed {
    if (sizeof($array) === 0)
      return null;
    return $array[0];
  }

  public static function lastOrNullFunc(array $array): mixed {
    if (sizeof($array) === 0)
      return null;
    return end($array);
  }

  public static function sumFunc(float|int|array ...$values): float {
    $arr = DefaultScope::mergeArraysRecursive($values);
    $res = 0.0;
    foreach ($arr as $value) {
      if (!is_numeric($value)) {
        throw new FormulaRuntimeException('Only numeric values or vectors are allowed for sum');
      }
      $res += (float) $value;
    }
    return $res;
  }

  public static function avgFunc(float|int|array ...$values): float {
    $sum = self::sumFunc($values);
    return $sum / (float) self::sizeofFunc($values);
  }

  public static function assertTrueFunc(bool $condition): void {
    if ($condition === false) {
      throw new FormulaRuntimeException('failed asserting that false is true');
    }
  }

  public static function assertEqualsFunc(mixed $expected, mixed $actual, string $message = ''): void {
    if ($expected != $actual) {
      throw new FormulaRuntimeException('failed asserting that ' . var_export($actual, true) . ' equals ' . var_export($expected, true) . ' ' . $message);
    }
  }

  public static function assertFalseFunc(bool $condition, ?string $message = null): void {
    if ($condition === true) {
      throw new FormulaRuntimeException('failed asserting that true is false'.($message !== null ? ('. Message: '.$message) : ''));
    }
  }
}
