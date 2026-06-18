<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Group\Provider\CustomerGroupLegacyDataProviderInterface;

class CustomerGroupFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly int $contextLanguageId,
        private readonly array $contextShopIds,
        private readonly CustomerGroupLegacyDataProviderInterface $legacyDataProvider,
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
            'category_reductions' => json_encode($this->legacyDataProvider->getCategoryReductions((int) $id, $this->contextLanguageId)),
            'authorized_modules' => json_encode($this->legacyDataProvider->getAuthorizedModuleIds((int) $id, $this->contextShopIds)),
        ];
    }

    public function getDefaultData(): array
    {
        $moduleIds = array_column($this->legacyDataProvider->getInstalledModules(), 'id_module');

        return [
            'name' => [],
            'reduction' => '0',
            'price_display_method' => 0,
            'show_prices' => true,
            'shop_association' => $this->contextShopIds,
            'category_reductions' => '[]',
            'authorized_modules' => json_encode($moduleIds),
        ];
    }
}
