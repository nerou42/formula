<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;

/**
 * @author Timo Lehnertz
 * @template-extends Parser<OuterFunctionArgument>
 */
class OuterFunctionArgumentParser extends Parser {

  public function __construct() {
    parent::__construct('outer function argument');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $parsedType = (new TypeParser(false))->parse($firstToken);
    $token = $parsedType->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    $varg = $token->id === Token::SPREAD;
    if($varg) {
      $token = $token->requireNext();
    }
    if($token->id === Token::IDENTIFIER) {
      $identifier = $token->value;
      $token = $token->next();
    } else {
      $identifier = null;
    }
    $arg = new OuterFunctionArgument($parsedType->parsed, false, $varg, $identifier);
    return new ParserReturn($arg, $token);
  }
}
