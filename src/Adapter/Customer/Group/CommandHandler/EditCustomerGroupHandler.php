<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use Category;
use Db;
use Group as CustomerGroup;
use GroupReduction;
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
            // Force id_shop_list with currently associated values to avoid clearing associations
            $customerGroup->id_shop_list = $this->customerGroupRepository->getAssociatedShopIds((int) $customerGroup->id);
        }

        $this->customerGroupValidator->validate($customerGroup);
        $this->customerGroupRepository->partialUpdate($customerGroup, $propertiesToUpdate);

        $groupId = $command->getCustomerGroupId()->getValue();

        if (null !== $command->getCategoryReductions()) {
            $this->saveCategoryReductions($groupId, $command);
        }

        if (null !== $command->getAuthorizedModuleIds()) {
            $this->saveModuleRestrictions($groupId, $customerGroup, $command);
        }
    }

    private function saveCategoryReductions(int $groupId, EditCustomerGroupCommand $command): void
    {
        $db = Db::getInstance();
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'group_reduction` WHERE `id_group` = ' . $groupId);
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'product_group_reduction_cache` WHERE `id_group` = ' . $groupId);

        foreach ($command->getCategoryReductions() as $categoryId => $reduction) {
            $category = new Category($categoryId);
            $category->addGroupsIfNoExist($groupId);

            $groupReduction = new GroupReduction();
            $groupReduction->id_group = $groupId;
            $groupReduction->id_category = $categoryId;
            $groupReduction->reduction = (float) (string) $reduction / 100;
            $groupReduction->save();
        }
    }

    private function saveModuleRestrictions(int $groupId, CustomerGroup $customerGroup, EditCustomerGroupCommand $command): void
    {
        $shopIds = $customerGroup->id_shop_list;

        CustomerGroup::addModulesRestrictions($groupId, $command->getAuthorizedModuleIds(), $shopIds);
    }
}
