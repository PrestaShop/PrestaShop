<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;

/**
 * Class AbstractDoctrineQueryBuilder provides most common dependencies of doctrine query builders.
 */
abstract class AbstractDoctrineQueryBuilder implements DoctrineQueryBuilderInterface
{
    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix
    ) {
    }

    /**
     * Escape percent in query for LIKE query
     *      '20%' => '20\%'
     *
     * @param string $value
     *
     * @return string
     */
    protected function escapePercent(string $value): string
    {
        return str_replace(
            '%',
            '\%',
            $value
        );
    }
}
