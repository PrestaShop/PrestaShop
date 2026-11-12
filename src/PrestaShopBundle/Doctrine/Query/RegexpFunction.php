<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Doctrine\Query;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * REGEXP(value, pattern): MySQL REGEXP operator, PostgreSQL "~" operator.
 */
final class RegexpFunction extends FunctionNode
{
    private Node $value;

    private Node $pattern;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->value = $parser->StringPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->pattern = $parser->StringExpression();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $operator = $sqlWalker->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform ? ' ~ ' : ' REGEXP ';

        return '(' . $this->value->dispatch($sqlWalker) . $operator . $this->pattern->dispatch($sqlWalker) . ')';
    }
}
