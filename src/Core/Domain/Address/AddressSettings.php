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

namespace PrestaShop\PrestaShop\Core\Domain\Address;

/**
 * Defines settings for addresses
 */
final class AddressSettings
{
    /**
     * Maximum allowed length of symbols for 'firstname' and 'lastname' fields
     */
    const MAX_NAME_LENGTH = 255;

    /**
     * Maximum allowed length of symbols for phone number (both phone and mobile_phone)
     */
    const MAX_PHONE_LENGTH = 32;

    /**
     * Maximum allowed length of symbols for address
     */
    const MAX_ADDRESS_LENGTH = 128;

    /**
     * Maximum allowed length of symbols for post code
     */
    const MAX_POST_CODE_LENGTH = 12;

    /**
     * Maximum allowed length of symbols for city name
     */
    const MAX_CITY_NAME_LENGTH = 64;

    /**
     * Maximum allowed length of symbols for field named 'other'
     */
    const MAX_OTHER_LENGTH = 300;
}
