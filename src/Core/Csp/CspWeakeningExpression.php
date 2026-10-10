<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Csp;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;

/**
 * The single definition of what makes a source "weakening": a weakening keyword or wildcard, a wildcard
 * host, or a broadening scheme on script-/style-src. The two grids (violations, allow-list) and the
 * pre-enforce weakening count all need the same rule in SQL, so it lives here once. It builds a SQL
 * fragment and binds its parameters; it runs no query.
 */
final class CspWeakeningExpression
{
    private function __construct()
    {
    }

    /**
     * A SQL boolean (1/0) flagging a weakening source. $prefix is the column prefix of the CSP table in
     * the query ('c.' when aliased, '' when not); callers append their own "AS is_weakening" alias.
     * Bind its named parameters with bindParameters().
     */
    public static function sql(string $prefix = ''): string
    {
        return sprintf(
            "IF(%1\$ssource IN (:weakeningSources) OR %1\$ssource LIKE '%%*%%'"
                . ' OR (%1$sdirective IN (:scriptStyleDirectives) AND %1$ssource IN (:broadeningSchemes)), 1, 0)',
            $prefix
        );
    }

    /** Binds the three array parameters that sql() references onto a DBAL query builder. */
    public static function bindParameters(QueryBuilder $qb): void
    {
        $qb->setParameter('weakeningSources', CspSource::WEAKENING_KEYWORDS, ArrayParameterType::STRING);
        $qb->setParameter('scriptStyleDirectives', [CspDirective::SCRIPT_SRC->value, CspDirective::STYLE_SRC->value], ArrayParameterType::STRING);
        $qb->setParameter('broadeningSchemes', CspSource::BROADENING_SCHEMES, ArrayParameterType::STRING);
    }
}
