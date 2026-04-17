<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
