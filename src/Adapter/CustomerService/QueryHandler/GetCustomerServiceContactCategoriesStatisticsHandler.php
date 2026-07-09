<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\QueryHandler;

use PrestaShop\PrestaShop\Adapter\CustomerService\Repository\CustomerThreadRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceContactCategoriesStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler\GetCustomerServiceContactCategoriesStatisticsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceContactCategoryStatistics;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetCustomerServiceContactCategoriesStatisticsHandler implements GetCustomerServiceContactCategoriesStatisticsHandlerInterface
{
    public function __construct(
        private readonly CustomerThreadRepository $customerThreadRepository,
    ) {
    }

    public function handle(GetCustomerServiceContactCategoriesStatistics $query): array
    {
        $shopConstraint = $query->getShopConstraint();
        $categories = $this->customerThreadRepository->getCustomerServiceContactCategories(
            $query->getLanguageId()->getValue(),
            $shopConstraint
        );
        $openThreadsByContact = $this->customerThreadRepository->countOpenThreadsGroupedByContact($shopConstraint);

        $statistics = [];
        foreach ($categories as $category) {
            $contactId = (int) $category['id_contact'];
            $stats = $openThreadsByContact[$contactId] ?? ['count' => 0, 'oldestThreadId' => null];

            $statistics[] = new CustomerServiceContactCategoryStatistics(
                $contactId,
                (string) $category['name'],
                (string) $category['description'],
                $stats['count'],
                $stats['oldestThreadId']
            );
        }

        return $statistics;
    }
}
