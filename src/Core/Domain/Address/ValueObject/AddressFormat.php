<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Address\ValueObject;

/**
 * Defines constants for address format which can be dynamic, depending from settings in country and customer address pages
 */
final class AddressFormat
{
    public const FIELD_ALIAS = 'alias';
    public const FIELD_CUSTOMER_EMAIL = 'customer_email';
    public const FIELD_CUSTOMER_FIRST_NAME = 'firstname';
    public const FIELD_CUSTOMER_LAST_NAME = 'lastname';
    public const FIELD_DNI = 'dni';
    public const FIELD_COMPANY = 'company';
    public const FIELD_VAT_NUMBER = 'vat_number';
    public const FIELD_ADDRESS_1 = 'address1';
    public const FIELD_ADDRESS_2 = 'address2';
    public const FIELD_POST_CODE = 'postcode';
    public const FIELD_COUNTRY = 'Country:name';
    public const FIELD_CITY = 'city';
    public const FIELD_STATE = 'state';
    public const FIELD_PHONE = 'phone';
    public const FIELD_PHONE_MOBILE = 'phone_mobile';
    public const FIELD_OTHER = 'other';

    /**
     * This class only stores domain values and it shouldn't be initialized
     */
    private function __construct()
    {
    }

    /**
     * @return string[]
     */
    public static function getAlwaysRequiredFields(): array
    {
        //from classes/AddressFormat::$requireFormFieldsList
        return [
            self::FIELD_CUSTOMER_FIRST_NAME,
            self::FIELD_CUSTOMER_LAST_NAME,
            self::FIELD_ADDRESS_1,
            self::FIELD_CITY,
            self::FIELD_COUNTRY,
        ];
    }

    /**
     * @return string[]
     */
    public static function getConfigurableRequiredFields(): array
    {
        return [
            self::FIELD_COMPANY,
            self::FIELD_ADDRESS_2,
            self::FIELD_POST_CODE,
            self::FIELD_OTHER,
            self::FIELD_PHONE,
            self::FIELD_PHONE_MOBILE,
            self::FIELD_VAT_NUMBER,
            self::FIELD_DNI,
        ];
    }

    /**
     * @return string[]
     */
    public static function getForbiddenFields(): array
    {
        // from legacy classes/AddressFormat::$forbiddenPropertyList
        return [
            'deleted',
            'date_add',
            'alias',
            'secure_key',
            'note',
            'newsletter',
            'ip_registration_newsletter',
            'newsletter_date_add',
            'optin',
            'passwd',
            'last_passwd_gen',
            'active',
            'is_guest',
            'date_upd',
            'country',
            'years',
            'days',
            'months',
            'description',
            'meta_description',
            'short_description',
            'link_rewrite',
            'meta_title',
            'meta_keywords',
            'display_tax_label',
            'need_zip_code',
            'contains_states',
            'call_prefixes',
            'show_public_prices',
            'max_payment',
            'max_payment_days',
            'geoloc_postcode',
            'logged',
            'account_number',
            'groupBox',
            'ape',
            'max_payment',
            'outstanding_allow_amount',
            'call_prefix',
            'definition',
            'debug_list',
        ];
    }
}
