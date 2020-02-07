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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Exception;

/**
 * Is thrown when currency constraints are violated
 */
class CurrencyConstraintException extends CurrencyException
{
    /**
     * Code used when the ISO code doesn't respect it's appropriate format
     *
     * @see AlphaIsoCode::PATTERN
     */
    const INVALID_ISO_CODE = 1;

    /**
     * Code used when an invalid exchange rate is used (positive float expected)
     *
     * @see ExchangeRate
     */
    const INVALID_EXCHANGE_RATE = 2;

    /**
     * Code used when trying to insert a currency already in database
     */
    const CURRENCY_ALREADY_EXISTS = 3;

    /**
     * Code used when an invalid liveExchangeRate is used (boolean expected)
     */
    const INVALID_LIVE_EXCHANGE_RATES = 4;

    /**
     * Code used when the numeric ISO code doesn't respect it's appropriate format
     *
     * @see NumericIsoCode::PATTERN
     */
    const INVALID_NUMERIC_ISO_CODE = 5;

    /**
     * Code used when trying to set an empty array of names
     */
    const EMPTY_NAME = 6;

    /**
     * Code used when trying to set an empty array of symbols
     */
    const EMPTY_SYMBOL = 7;

    /**
     * Code used when trying to insert a currency referenced in CLDR database but ISO code and
     * numeric ISO code are not matching the expected ones
     */
    const ISO_CODES_MISMATCH = 8;

    /**
     * Code used when an invalid precision is used (positive integer expected)
     *
     * @see Precision
     */
    const INVALID_PRECISION = 9;
}
