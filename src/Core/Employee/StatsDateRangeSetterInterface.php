<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Employee;

/**
 * Interface StatsDateRangeSetterInterface describes the persistence of the employee's
 * selected date range on the Stats page.
 */
interface StatsDateRangeSetterInterface
{
    /**
     * Persists the date range selected by the current employee on the Stats page.
     *
     * @param string $dateFrom
     * @param string $dateTo
     */
    public function setDateRange(string $dateFrom, string $dateTo): void;

    /**
     * Returns the date range currently selected by the employee on the Stats page.
     *
     * @return array{from: string, to: string}
     */
    public function getDateRange(): array;
}
