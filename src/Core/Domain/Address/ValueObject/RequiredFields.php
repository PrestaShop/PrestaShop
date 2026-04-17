<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\ValueObject;

/**
 * Stores configurable required field values for address.
 */
class RequiredFields
{
    public const REQUIRED_FIELD_COMPANY = 'company';
    public const REQUIRED_FIELD_ADDRESS_2 = 'address2';
    public const REQUIRED_FIELD_POST_CODE = 'postcode';
    public const REQUIRED_FIELD_OTHER = 'other';
    public const REQUIRED_FIELD_PHONE = 'phone';
    public const REQUIRED_FIELD_PHONE_MOBILE = 'phone_mobile';
    public const REQUIRED_FIELD_VAT_NUMBER = 'vat_number';
    public const REQUIRED_FIELD_DNI = 'dni';

    /**
     * Stores all allowed required fields to be configured for address
     */
    public const ALLOWED_REQUIRED_FIELDS = [
        self::REQUIRED_FIELD_COMPANY,
        self::REQUIRED_FIELD_ADDRESS_2,
        self::REQUIRED_FIELD_POST_CODE,
        self::REQUIRED_FIELD_OTHER,
        self::REQUIRED_FIELD_PHONE,
        self::REQUIRED_FIELD_PHONE_MOBILE,
        self::REQUIRED_FIELD_VAT_NUMBER,
        self::REQUIRED_FIELD_DNI,
    ];
}
