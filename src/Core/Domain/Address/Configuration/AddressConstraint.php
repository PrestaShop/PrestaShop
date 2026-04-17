<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Configuration;

/**
 * Stores address form constraints configuration values
 */
final class AddressConstraint
{
    /**
     * Maximum length for address alias (value is constrained by database)
     */
    public const MAX_ALIAS_LENGTH = 32;

    /**
     * Maximum length for company name (value is constrained by database)
     */
    public const MAX_COMPANY_LENGTH = 255;

    /**
     * Maximum length for last name (value is constrained by database)
     */
    public const MAX_LAST_NAME_LENGTH = 255;

    /**
     * Maximum length for first name (value is constrained by database)
     */
    public const MAX_FIRST_NAME_LENGTH = 255;

    /**
     * Maximum length for address (value is constrained by database)
     */
    public const MAX_ADDRESS_LENGTH = 255;

    /**
     * Maximum length for post code (value is constrained by database)
     */
    public const MAX_POSTCODE_LENGTH = 12;

    /**
     * Maximum length for city name (value is constrained by database)
     */
    public const MAX_CITY_LENGTH = 64;

    /**
     * Maximum length for other information
     */
    public const MAX_OTHER_LENGTH = 300;

    /**
     * Maximum length for phone number (value is constrained by database)
     */
    public const MAX_PHONE_LENGTH = 32;

    /**
     * Maximum length for VAT number (value is constrained by database)
     */
    public const MAX_VAT_LENGTH = 32;

    /**
     * Maximum length for identification number (value is constrained by database)
     */
    public const MAX_DNI_LENGTH = 16;

    /**
     * DNI field value regexp validation pattern
     */
    public const DNI_LITE_PATTERN = '/^[0-9A-Za-z-.]{1,16}$/U';

    /**
     * Prevents class to be instantiated
     */
    private function __construct()
    {
    }
}
