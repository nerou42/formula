<?php
namespace TimoLehnertz\formula\parsing;

/**
 * @author Timo Lehnertz
 */
class StatementParser extends VariantParser {

  public function __construct() {
    parent::__construct([new ExpressionStatementParser(),new CodeBlockParser(false, false),new VariableDeclarationStatementParser(),new ReturnStatementParser(),new WhileStatementParser(),new IfStatementParser(),new BreakStatementParser()]);
  }
}
