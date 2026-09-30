<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Exception;

class StoreConstraintException extends StoreException
{
    public const INVALID_ID = 10;
    public const INVALID_COUNTRY = 50;
    public const INVALID_STATE = 51;
    public const STATE_COUNTRY_MISMATCH = 52;
    public const STATE_NOT_IN_COUNTRY = 53;
    public const INVALID_SHOP_ASSOCIATION = 120;
}
