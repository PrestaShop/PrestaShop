<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

class CannotUpdateShopException extends ShopException
{
    public const SHOP_GROUP_LOCKED = 1;
    public const INVALID_TARGET_SHOP_GROUP = 2;
    public const FAILED_UPDATE = 10;
}
