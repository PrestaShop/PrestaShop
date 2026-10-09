<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

class ShopGroupConstraintException extends ShopGroupException
{
    public const INVALID_NAME = 1;
    public const INVALID_COLOR = 2;
    public const SHARE_ORDER_REQUIRES_SHARED_CUSTOMERS_AND_STOCK = 3;
}
