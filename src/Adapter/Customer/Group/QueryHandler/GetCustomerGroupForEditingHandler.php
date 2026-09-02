<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryHandler\GetCustomerGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;

#[AsQueryHandler]
class GetCustomerGroupForEditingHandler implements GetCustomerGroupForEditingHandlerInterface
{
    /**
     * @var GroupRepository
     */
    private $customerGroupRepository;

    public function __construct(GroupRepository $customerGroupRepository)
    {
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * @param GetCustomerGroupForEditing $query
     *
     * @return EditableCustomerGroup
     *
     * @throws GroupNotFoundException
     */
    public function handle(GetCustomerGroupForEditing $query): EditableCustomerGroup
    {
        $customerGroupId = $query->getCustomerGroupId();
        $customerGroup = $this->customerGroupRepository->get($customerGroupId);

        return new EditableCustomerGroup(
            $customerGroupId->getValue(),
            $customerGroup->name,
            new DecimalNumber($customerGroup->reduction),
            (bool) $customerGroup->price_display_method,
            (bool) $customerGroup->show_prices,
            $customerGroup->getAssociatedShops()
        );
    }
}
