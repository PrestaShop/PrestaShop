<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\QueryHandler;

use CustomerMessage;
use CustomerThread;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceListingStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler\GetCustomerServiceListingStatisticsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceListingStatistics;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetCustomerServiceListingStatisticsHandler implements GetCustomerServiceListingStatisticsHandlerInterface
{
    public function handle(GetCustomerServiceListingStatistics $query): CustomerServiceListingStatistics
    {
        return new CustomerServiceListingStatistics(
            CustomerThread::getTotalCustomerThreads(),
            CustomerThread::getTotalCustomerThreads('status = "open"'),
            CustomerThread::getTotalCustomerThreads('status IN ("pending1", "pending2")'),
            CustomerThread::getTotalCustomerThreads('status = "closed"'),
            CustomerMessage::getTotalCustomerMessages('id_employee = 0'),
            CustomerMessage::getTotalCustomerMessages('id_employee != 0'),
        );
    }
}
