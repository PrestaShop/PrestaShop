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
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\AddCustomerGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\CustomerGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsCommandHandler]
class AddCustomerGroupHandler implements AddCustomerGroupHandlerInterface
{
    public function __construct(
        private readonly CustomerGroupValidator $customerGroupValidator,
        private readonly GroupRepository $customerGroupRepository,
    ) {
    }

    public function handle(AddCustomerGroupCommand $command): CustomerGroupId
    {
        $customerGroup = new CustomerGroup();
        $customerGroup->name = $command->getLocalizedNames();
        $customerGroup->reduction = (string) $command->getReductionPercent();
        $customerGroup->price_display_method = (int) $command->displayPriceTaxExcluded();
        $customerGroup->show_prices = $command->showPrice();
        $customerGroup->id_shop_list = array_map(fn (ShopId $shopId) => $shopId->getValue(), $command->getShopIds());

        $this->customerGroupValidator->validate($customerGroup);

        $groupId = $this->customerGroupRepository->create($customerGroup);

        $this->saveCategoryReductions($groupId->getValue(), $command);
        $this->saveModuleRestrictions($groupId->getValue(), $command);

        return $groupId;
    }

    private function saveCategoryReductions(int $groupId, AddCustomerGroupCommand $command): void
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

    private function saveModuleRestrictions(int $groupId, AddCustomerGroupCommand $command): void
    {
        $shopIds = array_map(fn (ShopId $shopId) => $shopId->getValue(), $command->getShopIds());

        CustomerGroup::addModulesRestrictions($groupId, $command->getAuthorizedModuleIds(), $shopIds);
    }
}
