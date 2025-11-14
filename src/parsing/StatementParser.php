<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\FunctionStatement;
use TimoLehnertz\formula\statement\Statement;

/**
 * @author Timo Lehnertz
 * @template-extends VariantParser<Statement>
 */
class StatementParser extends VariantParser {

  public function __construct() {
    /**
     * @psalm-var Parser<FunctionStatement> $functionStatementParser
     */
    $functionStatementParser = new FunctionParser(true);
    parent::__construct('statement', [
      new CodeBlockParser(false, false),
      $functionStatementParser,
      new ExpressionStatementParser(),
      new VariableDeclarationStatementParser(),
      new ReturnStatementParser(),
      new WhileStatementParser(),
      new IfStatementParser(),
      new BreakStatementParser(),
      new ContinueStatementParser(),
      new DoWhileStatementParser(),
      new ForEachStatementParser(),
      new ForStatementParser(),
    ]);
  }
}
