<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;

/**
 * Provides the active shop groups as name => id pairs. With "exclude_groups_sharing_data_with_dependencies",
 * the groups sharing customers, orders or stock that already have customers or orders are left out: a shop
 * cannot be moved to them.
 */
final class ShopGroupChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    public function __construct(
        private readonly ShopGroupRepository $shopGroupRepository,
    ) {
    }

    public function getChoices(array $options = []): array
    {
        $shopGroups = $this->shopGroupRepository->getActiveShopGroups();

        if ($options['exclude_groups_sharing_data_with_dependencies'] ?? false) {
            $shopGroups = array_filter(
                $shopGroups,
                fn (array $shopGroup): bool => !$shopGroup['sharing_data']
                    || !$this->shopGroupRepository->hasCustomersOrOrders(new ShopGroupId($shopGroup['id_shop_group']))
            );
        }

        return FormChoiceFormatter::formatFormChoices($shopGroups, 'id_shop_group', 'name');
    }
}
