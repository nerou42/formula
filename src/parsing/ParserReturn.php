<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 * @template-covariant T
 */
class ParserReturn {

  /**
   * @var T
   */
  public readonly mixed $parsed;

  public readonly ?Token $nextToken;

  /**
   * @param T $parsed
   */
  public function __construct(mixed $parsed, ?Token $nextToken) {
    $this->parsed = $parsed;
    $this->nextToken = $nextToken;
  }
}
