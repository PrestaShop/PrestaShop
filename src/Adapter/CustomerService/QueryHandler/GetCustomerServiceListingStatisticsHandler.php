<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\QueryHandler;

use Db;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceListingStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler\GetCustomerServiceListingStatisticsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceListingStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use Shop;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetCustomerServiceListingStatisticsHandler implements GetCustomerServiceListingStatisticsHandlerInterface
{
    public function handle(GetCustomerServiceListingStatistics $query): CustomerServiceListingStatistics
    {
        $threadsByStatus = $this->countThreadsGroupedByStatus();
        $messagesByAuthor = $this->countMessagesGroupedByAuthor();

        return new CustomerServiceListingStatistics(
            array_sum($threadsByStatus),
            $threadsByStatus[CustomerThreadStatus::OPEN] ?? 0,
            ($threadsByStatus[CustomerThreadStatus::PENDING_1] ?? 0)
                + ($threadsByStatus[CustomerThreadStatus::PENDING_2] ?? 0),
            $threadsByStatus[CustomerThreadStatus::CLOSED] ?? 0,
            $messagesByAuthor['customer'] ?? 0,
            $messagesByAuthor['employee'] ?? 0,
        );
    }

    /**
     * Aggregates customer threads by status with a single GROUP BY query so
     * the page does not pay for one COUNT per status value.
     *
     * @return array<string, int>
     */
    private function countThreadsGroupedByStatus(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT status, COUNT(*) AS count
                FROM ' . _DB_PREFIX_ . 'customer_thread
                WHERE 1' . Shop::addSqlRestriction() . '
                GROUP BY status'
        );

        $counts = [];
        foreach ($rows ?: [] as $row) {
            $counts[(string) $row['status']] = (int) $row['count'];
        }

        return $counts;
    }

    /**
     * Aggregates customer messages by author kind (customer vs employee) in
     * a single grouped query, joined to customer_thread so multi-shop
     * scoping is preserved.
     *
     * @return array<string, int>
     */
    private function countMessagesGroupedByAuthor(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT IF(cm.id_employee = 0, "customer", "employee") AS author, COUNT(*) AS count
                FROM ' . _DB_PREFIX_ . 'customer_message cm
                LEFT JOIN ' . _DB_PREFIX_ . 'customer_thread ct
                    ON cm.id_customer_thread = ct.id_customer_thread
                WHERE 1' . Shop::addSqlRestriction() . '
                GROUP BY author'
        );

        $counts = [];
        foreach ($rows ?: [] as $row) {
            $counts[(string) $row['author']] = (int) $row['count'];
        }

        return $counts;
    }
}
