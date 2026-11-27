<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\DefaultCustomerGroupsRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetDefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryHandler\GetDefaultCustomerGroupsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\DefaultCustomerGroups;

#[AsQueryHandler]
final class GetDefaultCustomerGroupsHandler implements GetDefaultCustomerGroupsHandlerInterface
{
    public function __construct(
        private readonly DefaultCustomerGroupsRepository $defaultCustomerGroupsRepository,
    ) {
    }

    public function handle(GetDefaultCustomerGroups $query): DefaultCustomerGroups
    {
        return $this->defaultCustomerGroupsRepository->get($query->getShopConstraint());
    }
}
