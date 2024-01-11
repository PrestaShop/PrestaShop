<?php
/*
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

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use Group as CustomerGroup;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Validate\CustomerGroupValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\AddCustomerGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsCommandHandler]
class AddCustomerGroupHandler implements AddCustomerGroupHandlerInterface
{
    public function __construct(
        private readonly CustomerGroupValidator $customerGroupValidator,
        private readonly GroupRepository $customerGroupRepository
    ) {
    }

    public function handle(AddCustomerGroupCommand $command): GroupId
    {
        $customerGroup = new CustomerGroup();
        $customerGroup->name = $command->getLocalizedNames();
        $customerGroup->reduction = (string) $command->getReductionPercent();
        $customerGroup->price_display_method = (int) $command->displayPriceTaxExcluded();
        $customerGroup->show_prices = $command->showPrice();
        $customerGroup->id_shop_list = array_map(fn (ShopId $shopId) => $shopId->getValue(), $command->getShopIds());

        $this->customerGroupValidator->validate($customerGroup);

        return $this->customerGroupRepository->create($customerGroup);
    }
}
