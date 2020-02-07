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

namespace PrestaShop\PrestaShop\Core\Domain\Address\ValueObject;

/**
 * Stores configurable required field values for address.
 */
class RequiredFields
{
    const REQUIRED_FIELD_COMPANY = 'company';
    const REQUIRED_FIELD_ADDRESS_2 = 'address2';
    const REQUIRED_FIELD_POST_CODE = 'postcode';
    const REQUIRED_FIELD_OTHER = 'other';
    const REQUIRED_FIELD_PHONE = 'phone';
    const REQUIRED_FIELD_PHONE_MOBILE = 'phone_mobile';
    const REQUIRED_FIELD_VAT_NUMBER = 'vat_number';
    const REQUIRED_FIELD_DNI = 'dni';

    /**
     * Stores all allowed required fields to be configured for address
     */
    const ALLOWED_REQUIRED_FIELDS = [
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
