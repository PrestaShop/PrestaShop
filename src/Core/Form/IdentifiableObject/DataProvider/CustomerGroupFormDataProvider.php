<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Module\Query\GetInstalledModules;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\InstalledModule;

class CustomerGroupFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function getData($id): array
    {
        /** @var EditableCustomerGroup $group */
        $group = $this->queryBus->handle(new GetCustomerGroupForEditing((int) $id));

        /** @var InstalledModule[] $installedModules */
        $installedModules = $this->queryBus->handle(new GetInstalledModules());

        $authorizedModuleIds = $group->getAuthorizedModuleIds();

        $moduleRestrictions = array_map(
            static fn (InstalledModule $module) => [
                'id' => $module->getId(),
                'name' => $module->getName(),
                'authorized' => in_array($module->getId(), $authorizedModuleIds, true),
            ],
            $installedModules
        );

        $categoryReductions = [];
        foreach ($group->getCategoryReductions() as $categoryId => $entry) {
            $categoryReductions[] = [
                'id_category' => $categoryId,
                'name' => $entry['name'],
                'reduction' => (float) (string) $entry['reduction'],
            ];
        }

        return [
            'name' => $group->getLocalizedNames(),
            'reduction' => (string) $group->getReduction(),
            'price_display_method' => (int) $group->displayPriceTaxExcluded(),
            'show_prices' => $group->showPrice(),
            'shop_association' => $group->getShopIds(),
            'category_reductions' => $categoryReductions,
            'module_restrictions' => $moduleRestrictions,
        ];
    }

    public function getDefaultData(): array
    {
        /** @var InstalledModule[] $installedModules */
        $installedModules = $this->queryBus->handle(new GetInstalledModules());

        $moduleRestrictions = array_map(
            static fn (InstalledModule $module) => [
                'id' => $module->getId(),
                'name' => $module->getName(),
                'authorized' => true,
            ],
            $installedModules
        );

        return [
            'name' => [],
            'reduction' => '0',
            'price_display_method' => 0,
            'show_prices' => true,
            'shop_association' => $this->shopContext->getAssociatedShopIds(),
            'category_reductions' => [],
            'module_restrictions' => $moduleRestrictions,
        ];
    }
}
