<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

class CannotUpdateShopGroupException extends ShopGroupException
{
    public const SHARING_OPTIONS_LOCKED = 1;
    public const CANNOT_DISABLE_GROUP_WITH_SHOPS = 2;
}
