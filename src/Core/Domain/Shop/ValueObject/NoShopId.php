<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

/**
 * Represents relation value when shop is not specified
 */
class NoShopId implements ShopIdInterface
{
    /**
     * Value when shop is not specified
     */
    public const NO_SHOP_ID = 0;

    /**
     * @return int
     */
    public function getValue(): int
    {
        return self::NO_SHOP_ID;
    }
}
