<?php
declare(strict_types=1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\ConstantExpression;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\DateTimeImmutableType;
use TimoLehnertz\formula\type\DateTimeImmutableValue;
use TimoLehnertz\formula\type\DateIntervalValue;
use TimoLehnertz\formula\type\DateIntervalType;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;

/**
 * @author Timo Lehnertz
 * @template-extends Parser<ConstantExpression>
 */
class ConstantExpressionParser extends Parser {

  public function __construct() {
    parent::__construct('constant expression');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    return match($firstToken->id) {
      Token::FLOAT_CONSTANT => new ParserReturn(new ConstantExpression(new FloatType(), new FloatValue(floatval($firstToken->value)), $firstToken->value), $firstToken->next()),
      Token::INT_CONSTANT => new ParserReturn(new ConstantExpression(new IntegerType(), new IntegerValue(intval($firstToken->value)), $firstToken->value), $firstToken->next()),
      Token::KEYWORD_FALSE => new ParserReturn(new ConstantExpression(new BooleanType(), new BooleanValue(false), $firstToken->value), $firstToken->next()),
      Token::KEYWORD_TRUE => new ParserReturn(new ConstantExpression(new BooleanType(), new BooleanValue(true), $firstToken->value), $firstToken->next()),
      Token::STRING_CONSTANT => new ParserReturn(new ConstantExpression(new StringType(), new StringValue($firstToken->value), "'" . $firstToken->value . "'"), $firstToken->next()),
      Token::KEYWORD_NULL => new ParserReturn(new ConstantExpression(new NullType(), new NullValue(), $firstToken->value), $firstToken->next()),
      Token::DATE_TIME => new ParserReturn(new ConstantExpression(new DateTimeImmutableType(), new DateTimeImmutableValue(new \DateTimeImmutable($firstToken->value)), "'" . $firstToken->value . "'"), $firstToken->next()),
      Token::DATE_INTERVAL => new ParserReturn(new ConstantExpression(new DateIntervalType(), new DateIntervalValue(new \DateInterval($firstToken->value)), "'" . $firstToken->value . "'"), $firstToken->next()),
      Token::KEYWORD_VOID => new ParserReturn(new ConstantExpression(new VoidType(), new VoidValue(), 'void'), $firstToken->next()),
      default => throw new ParsingSkippedException()
    };
  }
}
