<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Shop;

/**
 * Interface ShopThemesNamesProviderInterface
 */
interface ShopThemesNamesProviderInterface
{
    /**
     * Get theme names used by shops.
     *
     * @return list<string>
     */
    public function getShopThemesNames(): array;
}
