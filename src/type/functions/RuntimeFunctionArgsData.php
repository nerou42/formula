<?php
declare(strict_types=1);

namespace TimoLehnertz\formula\type\functions;

/**
 * @author Timo Lehnertz
 */
class RuntimeFunctionArgsData {

  /**
   * Determines which arguments accept a Value instance and don't need to be converted to a php value before passing them to the callback.
   * @var array<int, mixed>
   */
  private readonly array $valueArgs;


  /**
   * If the last argument is variadic and accepts a Value, then this indicates its index
   * @var integer
   */
  private readonly ?int $valueVarg;

  /**
   * @param array<int, mixed> $valueArgs
   */
  public function __construct(array $valueArgs = [], ?int $valueVarg = null) {
    $this->valueArgs = $valueArgs;
    $this->valueVarg = $valueVarg;
  }

  public function argAcceptsValue(int $index): bool {
    return isset($this->valueArgs[$index]) || ($this->valueVarg !== null && $index >= $this->valueVarg);
  }
}
