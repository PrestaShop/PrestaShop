<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetDefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\DefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Flags the default customer groups as unremovable, so the grid hides their delete action and bulk checkbox.
 */
final class CustomerGroupsGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $customerGroupsDataFactory,
        private readonly CommandBusInterface $queryBus,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $gridData = $this->customerGroupsDataFactory->getData($searchCriteria);

        /** @var DefaultCustomerGroups $defaultGroups */
        $defaultGroups = $this->queryBus->handle(new GetDefaultCustomerGroups($this->shopContext->getShopConstraint()));
        $defaultGroupIds = [
            $defaultGroups->getVisitorsGroupId(),
            $defaultGroups->getGuestsGroupId(),
            $defaultGroups->getCustomersGroupId(),
        ];

        $records = [];
        foreach ($gridData->getRecords() as $record) {
            $record['unremovable'] = in_array((int) $record['id_group'], $defaultGroupIds, true);
            $records[] = $record;
        }

        return new GridData(
            new RecordCollection($records),
            $gridData->getRecordsTotal(),
            $gridData->getQuery()
        );
    }
}
