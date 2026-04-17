<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

/**
 * Represents a relation identifier of Shop
 * This interface allows to explicitly define whether the shop relation is optional or required.
 *
 * @see ShopId
 * @see NoShopId
 */
interface ShopIdInterface
{
    /**
     * @return int
     */
    public function getValue(): int;
}
