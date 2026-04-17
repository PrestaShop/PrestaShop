<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

/**
 * Stores required field values for customer.
 */
class RequiredField
{
    /**
     * Partner offers field name
     */
    public const PARTNER_OFFERS = 'optin';

    /**
     * Newsletter field name
     */
    public const NEWSLETTER = 'newsletter';

    /**
     * All allowed required fields for customer
     */
    public const ALLOWED_REQUIRED_FIELDS = [
        self::PARTNER_OFFERS,
        self::NEWSLETTER,
    ];
}
