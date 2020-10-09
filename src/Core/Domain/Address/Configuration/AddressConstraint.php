<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    const MAX_ALIAS_LENGTH = 32;

    /**
     * Maximum length for company name (value is constrained by database)
     */
    const MAX_COMPANY_LENGTH = 255;

    /**
     * Maximum length for last name (value is constrained by database)
     */
    const MAX_LAST_NAME_LENGTH = 255;

    /**
     * Maximum length for first name (value is constrained by database)
     */
    const MAX_FIRST_NAME_LENGTH = 255;

    /**
     * Maximum length for address (value is constrained by database)
     */
    const MAX_ADDRESS_LENGTH = 255;

    /**
     * Maximum length for post code (value is constrained by database)
     */
    const MAX_POSTCODE_LENGTH = 12;

    /**
     * Maximum length for city name (value is constrained by database)
     */
    const MAX_CITY_LENGTH = 64;

    /**
     * Maximum length for other information
     */
    const MAX_OTHER_LENGTH = 300;

    /**
     * Maximum length for phone number (value is constrained by database)
     */
    const MAX_PHONE_LENGTH = 32;

    /**
     * Maximum length for VAT number (value is constrained by database)
     */
    const MAX_VAT_LENGTH = 32;

    /**
     * Maximum length for identification number (value is constrained by database)
     */
    const MAX_DNI_LENGTH = 16;

    /**
     * DNI field value regexp validation pattern
     */
    const DNI_LITE_PATTERN = '/^[0-9A-Za-z-.]{1,16}$/U';

    /**
     * Prevents class to be instantiated
     */
    private function __construct()
    {
    }
}
