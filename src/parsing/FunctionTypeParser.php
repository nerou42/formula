<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;

/**
 * @author Timo Lehnertz
 * @template-extends Parser<FunctionType>
 */
class FunctionTypeParser extends Parser {

  public function __construct() {
    parent::__construct('function type');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_FUNCTION) {
      throw new ParsingSkippedException();
    }
    $parsedParameters = (new EnumeratedParser('function args', new OuterFunctionArgumentParser(), Token::BRACKETS_OPEN, Token::COMMA, Token::BRACKETS_CLOSED, false, true))->parse($firstToken->next());
    $arguments = $parsedParameters->parsed;
    $token = $parsedParameters->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::FUNCTION_ARROW) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $token, 'Expected ->');
    }
    $token = $token->requireNext();
    $parsedReturnType = (new TypeParser(false))->parse($token);
    $isVArgs = false;
    foreach($arguments as $key => $argument) {
      if($argument->varg) {
        if($isVArgs) {
          throw new ParsingException(ParsingException::ERROR_VARG_NOT_LAST, $token);
        }
        $arguments[$key] = $argument->setOptional(true);
        $isVArgs = true;
      }
    }
    $argumentsListType = new OuterFunctionArgumentListType($arguments, $isVArgs);
    $functionType = new FunctionType($argumentsListType, $parsedReturnType->parsed);
    return new ParserReturn($functionType, $parsedReturnType->nextToken);
  }
}
