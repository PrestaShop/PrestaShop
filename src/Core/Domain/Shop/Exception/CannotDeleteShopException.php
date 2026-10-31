<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

class CannotDeleteShopException extends ShopException
{
    public const DEFAULT_SHOP = 1;
    public const HAS_DEPENDENCIES = 2;
    public const FAILED_DELETE = 10;
}
