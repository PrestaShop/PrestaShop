<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Interface for service that applies filters for Doctrine query builder
 */
interface DoctrineFilterApplicatorInterface
{
    /**
     * @param QueryBuilder $qb
     * @param SqlFilters $filters
     * @param array $filterValues
     */
    public function apply(QueryBuilder $qb, SqlFilters $filters, array $filterValues);
}
