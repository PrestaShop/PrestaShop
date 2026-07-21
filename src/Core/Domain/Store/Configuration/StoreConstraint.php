<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Store\Configuration;

/**
 * Stores address form constraints configuration values
 */
final class StoreConstraint
{
    /**
     * Maximum length for phone number (value is constrained by database)
     */
    public const MAX_PHONE_LENGTH = 16;
    /**
     * Maximum length for postcode (value is constrained by database)
     */
    public const MAX_POSTCODE_LENGTH = 12;

    /**
     * Prevents class to be instantiated
     */
    private function __construct()
    {
    }
}
