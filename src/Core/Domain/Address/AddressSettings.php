<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address;

/**
 * Defines settings for addresses
 */
final class AddressSettings
{
    /**
     * Maximum allowed length of symbols for 'firstname' and 'lastname' fields
     */
    public const MAX_NAME_LENGTH = 255;

    /**
     * Maximum allowed length of symbols for phone number (both phone and mobile_phone)
     */
    public const MAX_PHONE_LENGTH = 32;

    /**
     * Maximum allowed length of symbols for address
     */
    public const MAX_ADDRESS_LENGTH = 128;

    /**
     * Maximum allowed length of symbols for post code
     */
    public const MAX_POST_CODE_LENGTH = 12;

    /**
     * Maximum allowed length of symbols for city name
     */
    public const MAX_CITY_NAME_LENGTH = 64;

    /**
     * Maximum allowed length of symbols for field named 'other'
     */
    public const MAX_OTHER_LENGTH = 300;
}
