<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 * @template-covariant T
 * @template-extends Parser<T>
 */
class VariantParser extends Parser {

  /**
   * @psalm-var Parser<T>[]
   */
  private readonly array $parsers;

  /**
   * @psalm-param Parser<T>[] $parsers
   */
  public function __construct(string $name, array $parsers) {
    parent::__construct($name);
    $this->parsers = $parsers;
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    foreach($this->parsers as $parser) {
      try {
        return $parser->parse($firstToken);
      } catch(ParsingSkippedException) {} // try the next one
    }
    throw new ParsingSkippedException();
  }
}
