<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Validate\CustomerGroupValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\EditCustomerGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsCommandHandler]
class EditCustomerGroupHandler implements EditCustomerGroupHandlerInterface
{
    public function __construct(
        private readonly CustomerGroupValidator $customerGroupValidator,
        private readonly GroupRepository $customerGroupRepository
    ) {
    }

    public function handle(EditCustomerGroupCommand $command): void
    {
        $customerGroup = $this->customerGroupRepository->get($command->getCustomerGroupId());

        $propertiesToUpdate = [];
        if (null !== $command->getLocalizedNames()) {
            $customerGroup->name = $command->getLocalizedNames();
            $propertiesToUpdate['name'] = array_keys($command->getLocalizedNames());
        }

        if (null !== $command->getReductionPercent()) {
            $customerGroup->reduction = (string) $command->getReductionPercent();
            $propertiesToUpdate[] = 'reduction';
        }

        if (null !== $command->displayPriceTaxExcluded()) {
            $customerGroup->price_display_method = (int) $command->displayPriceTaxExcluded();
            $propertiesToUpdate[] = 'price_display_method';
        }

        if (null !== $command->showPrice()) {
            $customerGroup->show_prices = $command->showPrice();
            $propertiesToUpdate[] = 'show_prices';
        }

        if (null !== $command->getShopIds()) {
            $customerGroup->id_shop_list = array_map(fn (ShopId $shopId) => $shopId->getValue(), $command->getShopIds());
        } else {
            // We force the id_shop_list with the currently associated values or the associations will be messed with whatever is in the legacy context
            $customerGroup->id_shop_list = $this->customerGroupRepository->getAssociatedShopIds((int) $customerGroup->id);
        }

        $this->customerGroupValidator->validate($customerGroup);
        $this->customerGroupRepository->partialUpdate($customerGroup, $propertiesToUpdate);
    }
}
