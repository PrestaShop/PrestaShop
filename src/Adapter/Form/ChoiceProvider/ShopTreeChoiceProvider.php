<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Shop;
use ShopGroup;

/**
 * Class ShopTreeChoiceProvider provides shop choices for choice tree.
 *
 * @internal
 */
final class ShopTreeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $shopGroups = ShopGroup::getShopGroups();
        $choices = [];

        /** @var ShopGroup $shopGroup */
        foreach ($shopGroups as $shopGroup) {
            $shopGroupChoices = [
                'name' => $shopGroup->name,
                'id_shop' => null,
                'children' => [],
            ];

            $shops = ShopGroup::getShopsFromGroup($shopGroup->id);

            foreach ($shops as $shopId) {
                $shop = Shop::getShop($shopId['id_shop']);

                // If context employee doesn't have access to a shop, $shop will be false
                if (false === $shop) {
                    continue;
                }

                $shopGroupChoices['children'][] = [
                    'name' => $shop['name'],
                    'id_shop' => $shop['id_shop'],
                ];
            }

            $choices[] = $shopGroupChoices;
        }

        return $choices;
    }
}
