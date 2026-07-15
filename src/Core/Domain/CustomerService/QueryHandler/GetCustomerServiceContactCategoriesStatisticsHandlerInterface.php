<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceContactCategoriesStatistics;

interface GetCustomerServiceContactCategoriesStatisticsHandlerInterface
{
    /**
     * @return \PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceContactCategoryStatistics[]
     */
    public function handle(GetCustomerServiceContactCategoriesStatistics $query): array;
}
