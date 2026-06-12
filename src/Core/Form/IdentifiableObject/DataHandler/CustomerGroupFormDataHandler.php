<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use Category;
use Db;
use Group as CustomerGroup;
use GroupReduction;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class CustomerGroupFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly array $contextShopIds,
    ) {
    }

    public function create(array $data): int
    {
        /** @var GroupId $groupId */
        $groupId = $this->commandBus->handle(new AddCustomerGroupCommand(
            array_filter($data['name'], static fn (?string $name): bool => $name !== null && $name !== ''),
            new DecimalNumber((string) ($data['reduction'] ?? '0')),
            (bool) $data['price_display_method'],
            (bool) ($data['show_prices'] ?? true),
            $data['shop_association'] ?? $this->contextShopIds
        ));

        $id = $groupId->getValue();
        $this->saveCategoryReductions($id, $data);
        $this->saveModuleRestrictions($id, $data);

        return $id;
    }

    public function update($id, array $data): void
    {
        $command = (new EditCustomerGroupCommand((int) $id))
            ->setLocalizedNames(array_filter($data['name'], static fn (?string $name): bool => $name !== null && $name !== ''))
            ->setReductionPercent(new DecimalNumber((string) ($data['reduction'] ?? '0')))
            ->setDisplayPriceTaxExcluded((bool) $data['price_display_method'])
            ->setShowPrice((bool) ($data['show_prices'] ?? true))
        ;

        if (isset($data['shop_association'])) {
            $command->setShopIds(array_map('intval', $data['shop_association']));
        }

        $this->commandBus->handle($command);

        $this->saveCategoryReductions((int) $id, $data);
        $this->saveModuleRestrictions((int) $id, $data);
    }

    private function saveCategoryReductions(int $groupId, array $data): void
    {
        $db = Db::getInstance();
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'group_reduction` WHERE `id_group` = ' . (int) $groupId);
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'product_group_reduction_cache` WHERE `id_group` = ' . (int) $groupId);

        $reductions = json_decode($data['category_reductions'] ?? '[]', true);
        if (empty($reductions)) {
            return;
        }

        foreach ($reductions as $item) {
            $idCategory = (int) ($item['id_category'] ?? 0);
            $reduction = (float) ($item['reduction'] ?? 0);

            if ($idCategory <= 0 || $reduction < 0 || $reduction > 100) {
                continue;
            }

            $category = new Category($idCategory);
            $category->addGroupsIfNoExist($groupId);

            $groupReduction = new GroupReduction();
            $groupReduction->id_group = $groupId;
            $groupReduction->id_category = $idCategory;
            $groupReduction->reduction = $reduction / 100;
            $groupReduction->save();
        }
    }

    private function saveModuleRestrictions(int $groupId, array $data): void
    {
        $authorizedIds = json_decode($data['authorized_modules'] ?? '[]', true);
        if (!is_array($authorizedIds)) {
            return;
        }

        CustomerGroup::addModulesRestrictions($groupId, array_map('intval', $authorizedIds), $this->contextShopIds);
    }
}
