<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\QueryHandler;

use PrestaShop\PrestaShop\Adapter\CustomerService\Repository\CustomerThreadRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceListingStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler\GetCustomerServiceListingStatisticsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceListingStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetCustomerServiceListingStatisticsHandler implements GetCustomerServiceListingStatisticsHandlerInterface
{
    public function __construct(
        private readonly CustomerThreadRepository $customerThreadRepository,
    ) {
    }

    public function handle(GetCustomerServiceListingStatistics $query): CustomerServiceListingStatistics
    {
        $shopConstraint = $query->getShopConstraint();
        $threadsByStatus = $this->customerThreadRepository->countThreadsGroupedByStatus($shopConstraint);
        $messagesByAuthor = $this->customerThreadRepository->countMessagesGroupedByAuthor($shopConstraint);

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
}
