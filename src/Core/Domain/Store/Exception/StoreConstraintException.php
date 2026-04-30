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
    public const INVALID_NAME = 20;
    public const MISSING_NAME_FOR_DEFAULT_LANGUAGE = 21;
    public const INVALID_ADDRESS = 30;
    public const INVALID_CITY = 40;
    public const INVALID_COUNTRY = 50;
    public const INVALID_STATE = 51;
    public const STATE_COUNTRY_MISMATCH = 52;
    public const INVALID_POSTCODE = 60;
    public const INVALID_COORDINATE = 70;
    public const MISSING_COORDINATE = 71;
    public const INVALID_PHONE = 80;
    public const INVALID_EMAIL = 90;
    public const INVALID_HOURS = 100;
    public const INVALID_NOTE = 110;
    public const INVALID_SHOP_ASSOCIATION = 120;
}
