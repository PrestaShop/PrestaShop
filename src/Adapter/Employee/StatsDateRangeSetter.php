<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Employee\StatsDateRangeSetterInterface;

/**
 * Class StatsDateRangeSetter handles persisting the Stats page date range for the context employee.
 */
final class StatsDateRangeSetter implements StatsDateRangeSetterInterface
{
    public function __construct(
        private readonly LegacyContext $legacyContext
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setDateRange(string $dateFrom, string $dateTo): void
    {
        $employee = $this->legacyContext->getContext()->employee;
        $employee->stats_date_from = $dateFrom;
        $employee->stats_date_to = $dateTo;
        $employee->update();
    }

    /**
     * {@inheritdoc}
     */
    public function getDateRange(): array
    {
        $employee = $this->legacyContext->getContext()->employee;

        return [
            'from' => (string) $employee->stats_date_from,
            'to' => (string) $employee->stats_date_to,
        ];
    }
}
