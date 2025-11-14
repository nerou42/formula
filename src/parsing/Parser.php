<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\Statement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 * @template-covariant T
 */
abstract class Parser {

  public readonly string $name;

  public function __construct(string $name) {
    $this->name = $name;
  }

  /**
   * @throws ParsingException
   * @throws ParsingSkippedException
   * @psalm-return ParserReturn<T>
   */
  public function parse(?Token $firstToken, bool $required = false, bool $expectEnd = false): ParserReturn {
    if($firstToken === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    ParsingException::setParser($this, $firstToken);
    try {
      $parserReturn = $this->parsePart($firstToken);
      if($parserReturn->parsed instanceof Statement) {
        $parserReturn->parsed->setFirstToken($firstToken);
      }
    } catch(ParsingSkippedException $e) {
      if($required) {
        throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken);
      } else {
        throw $e;
      }
    }
    if($expectEnd && $parserReturn->nextToken !== null) {
      throw new ParsingException(ParsingException::ERROR_EXPECTED_EOF, $parserReturn->nextToken);
    }
    return $parserReturn;
  }

  /**
   * @throws ParsingException
   * @throws ParsingSkippedException
   * @psalm-return ParserReturn<T>
   */
  protected abstract function parsePart(Token $firstToken): ParserReturn;
}
