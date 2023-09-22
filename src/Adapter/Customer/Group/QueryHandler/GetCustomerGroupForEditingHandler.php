<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     * @throws GroupNotFoundException
     *
     * @return EditableCustomerGroup
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
