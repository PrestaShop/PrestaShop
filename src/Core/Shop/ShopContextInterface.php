<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Shop;

/**
 * Interface ShopContextInterface
 */
interface ShopContextInterface
{
    /**
     * Get name for context shop.
     *
     * @return string
     */
    public function getShopName();

    /**
     * @return int[]
     */
    public function getContextShopIds(): array;
}
