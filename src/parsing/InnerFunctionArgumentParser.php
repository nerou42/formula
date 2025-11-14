<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\functions\InnerFunctionArgument;
use TimoLehnertz\formula\type\functions\InnerVargFunctionArgument;

/**
 * @author Timo Lehnertz
 * @template-extends Parser<InnerVargFunctionArgument|InnerFunctionArgument>
 */
class InnerFunctionArgumentParser extends Parser {

  public function __construct() {
    parent::__construct('function argument');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $token = $firstToken;
    $final = $firstToken->id === Token::KEYWORD_FINAL;
    if($final) {
      $token = $token->requireNext();
    }
    $parsedType = (new TypeParser(true))->parse($token);
    $token = $parsedType->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    $isVarg = false;
    if($token->id === Token::SPREAD) {
      $isVarg = true;
      $token = $token->requireNext();
    }
    if($token->id !== Token::IDENTIFIER) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $token, 'Expected identifier');
    }
    $identifier = $token->value;
    $parsedExpression = null;
    $next = $token->next();
    if($next !== null && $next->id === Token::ASSIGNMENT) {
      if($isVarg) {
        throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $token, 'Vargs can\'t have a default initilizer');
      }
      $parsedExpression = (new ExpressionParser())->parse($next->next());
      $token = $parsedExpression->nextToken;
    } else {
      $token = $token->next();
    }
    if($isVarg) {
      $parsed = new InnerVargFunctionArgument($final, $parsedType->parsed, $identifier);
    } else {
      $parsed = new InnerFunctionArgument($final, $parsedType->parsed, $identifier, $parsedExpression?->parsed ?? null);
    }
    return new ParserReturn($parsed, $token);
  }
}
