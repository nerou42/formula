<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class VariantParser extends Parser {

  /**
   * @var Parser[]
   */
  private readonly array $parsers;

  /**
   * @param Parser[] $parsers
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
