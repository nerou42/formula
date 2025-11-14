<?php
declare(strict_types=1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\FormulaException;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ParsingException extends FormulaException {

  public const ERROR_UNEXPECTED_END_OF_INPUT = 1;

  public const ERROR_TOO_MANY_DELIMITERS = 2;

  public const ERROR_MISSING_DELIMITERS = 3;

  public const ERROR_INVALID_TYPE = 4;

  public const ERROR_INVALID_OPERATOR_USE = 5;

  public const ERROR_EXPECTED_EOF = 7;

  public const ERROR_INCOMPLETE_TERNARY = 8;

  public const ERROR_UNEXPECTED_TOKEN = 10;

  public const ERROR_TOO_MANY_ELSE = 11;

  public const ERROR_VARG_NOT_LAST = 12;

  public readonly ?Parser $parser;

  /** @psalm-suppress PossiblyUnusedProperty */
  public readonly int $parsingErrorCode;

  public readonly ?Token $token;

  /** @psalm-suppress PossiblyUnusedProperty */
  public readonly ?string $additionalInfo;

  private static ?Parser $currentParser = null;

  private static ?Token $currentToken = null;

  /**
   * @param ParsingException::ERROR_* extends int $parsingErrorCode
   */
  public function __construct(int $parsingErrorCode, ?Token $token = null, ?string $additionalInfo = null) {
    $this->parsingErrorCode = $parsingErrorCode;
    $this->token = $token ?? ParsingException::$currentToken;
    $this->additionalInfo = $additionalInfo;
    $this->parser = ParsingException::$currentParser;

    $parserName = $this->parser?->name ?? 'Unknown';
    $line = $this->token !== null ? $this->token->line + 1 : 'unknown';
    $position = $this->token !== null ? $this->token->position + 1 : 'unknown';

    $message = 'Syntax error in ' . $parserName . ': ' . $line . ':' . $position . ' ' . ($this->token?->value ?? '-') . ' . Message: ' . static::codeToMessage($parsingErrorCode);
    if ($additionalInfo !== null) {
      $message .= '. ' . $additionalInfo;
    }
    parent::__construct($message);
  }

  public static function setParser(Parser $currentParser, Token $currentToken): void {
    ParsingException::$currentParser = $currentParser;
    ParsingException::$currentToken = $currentToken;
  }

  private static function codeToMessage(int $parsingErrorCode): string {
    return match($parsingErrorCode) {
      static::ERROR_UNEXPECTED_END_OF_INPUT => 'Unexpected end of input',
      static::ERROR_TOO_MANY_DELIMITERS => 'Too many delimiters',
      static::ERROR_MISSING_DELIMITERS => 'Missing delimiter',
      static::ERROR_INVALID_TYPE => 'Invalid type',
      static::ERROR_INVALID_OPERATOR_USE => 'Invalid use of operator',
      static::ERROR_EXPECTED_EOF => 'Expected ond of file',
      static::ERROR_INCOMPLETE_TERNARY => 'Incomplete ternary expression',
      static::ERROR_UNEXPECTED_TOKEN => 'Unexpected token',
      static::ERROR_TOO_MANY_ELSE => 'Else block can\'t follow else block',
      static::ERROR_VARG_NOT_LAST => 'Varg argument must be last',
      default => throw new \UnexpectedValueException($parsingErrorCode . ' is no valid ParsingErrorCode')
    };
  }
}
