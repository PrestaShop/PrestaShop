<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

class CannotDeleteShopUrlException extends ShopUrlException
{
    public const FAILED_DELETE = 1;
    public const MAIN_URL = 2;
}
