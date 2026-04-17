<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query\Filter;

/**
 * Stores filters to be applied on SQL query
 */
final class SqlFilters
{
    public const WHERE_STRICT = 1;
    public const WHERE_LIKE = 2;
    public const HAVING_LIKE = 3;
    public const WHERE_DATE = 4;
    public const MIN_MAX = 5;

    /** @var array */
    private $filters = [];

    /**
     * @param string $filterName
     * @param string $sqlField
     * @param int $comparison
     *
     * @return self
     */
    public function addFilter($filterName, $sqlField, $comparison = self::WHERE_STRICT): self
    {
        $this->filters[] = [
            'filter_name' => $filterName,
            'sql_field' => $sqlField,
            'comparison' => $comparison,
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
