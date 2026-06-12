<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use GroupReduction;
use Module;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;

class CustomerGroupFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly int $contextLanguageId,
        private readonly array $contextShopIds,
    ) {
    }

    public function getData($id): array
    {
        /** @var EditableCustomerGroup $group */
        $group = $this->queryBus->handle(new GetCustomerGroupForEditing((int) $id));

        return [
            'name' => $group->getLocalizedNames(),
            'reduction' => (string) $group->getReduction(),
            'price_display_method' => (int) $group->displayPriceTaxExcluded(),
            'show_prices' => $group->showPrice(),
            'shop_association' => $group->getShopIds(),
            'category_reductions' => $this->loadCategoryReductions((int) $id),
            'authorized_modules' => $this->loadAuthorizedModuleIds((int) $id),
        ];
    }

    public function getDefaultData(): array
    {
        return [
            'name' => [],
            'reduction' => '0',
            'price_display_method' => 0,
            'show_prices' => true,
            'shop_association' => $this->contextShopIds,
            'category_reductions' => '[]',
            'authorized_modules' => $this->loadAllModuleIdsAsAuthorized(),
        ];
    }

    private function loadCategoryReductions(int $groupId): string
    {
        $groupReductions = GroupReduction::getGroupReductions($groupId, $this->contextLanguageId);
        $result = [];
        foreach ($groupReductions as $row) {
            $result[] = [
                'id_category' => (int) $row['id_category'],
                'reduction' => (float) $row['reduction'] * 100,
                'name' => $row['name'] ?? '',
            ];
        }

        return json_encode($result);
    }

    private function loadAuthorizedModuleIds(int $groupId): string
    {
        $authorizedModules = Module::getAuthorizedModules($groupId, $this->contextShopIds);
        if (!is_array($authorizedModules)) {
            return $this->loadAllModuleIdsAsAuthorized();
        }

        return json_encode(array_column($authorizedModules, 'id_module'));
    }

    private function loadAllModuleIdsAsAuthorized(): string
    {
        $modules = Module::getModulesInstalled();

        return json_encode(array_column($modules, 'id_module'));
    }
}
