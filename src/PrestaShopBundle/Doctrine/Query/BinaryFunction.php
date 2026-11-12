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
 * BINARY(expr) forces a case-sensitive comparison on MySQL; PostgreSQL comparisons already are.
 */
final class BinaryFunction extends FunctionNode
{
    private Node $stringPrimary;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->stringPrimary = $parser->StringPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $expression = $this->stringPrimary->dispatch($sqlWalker);
        if ($sqlWalker->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            return $expression;
        }

        return 'BINARY(' . $expression . ')';
    }
}
