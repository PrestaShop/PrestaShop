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

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

/**
 * Value-object representing an identifier for a currency, "translated" in a given locale (language + region).
 */
class LocalizedCurrencyId
{
    /**
     * ISO 4217 code of the currency.
     *
     * @var string
     */
    private $currencyCode;

    /**
     * CurrencyData's data is translated in this locale.
     * IETF tag (e.g.: fr-FR, en-US...).
     *
     * @var string
     */
    private $localeCode;

    /**
     * @param string $currencyCode
     *                             ISO 4217 currency code
     * @param $localeCode
     *  IETF tag (e.g.: fr-FR, en-US...)
     */
    public function __construct($currencyCode, $localeCode)
    {
        $this->currencyCode = $currencyCode;
        $this->localeCode = $localeCode;
    }

    public function __toString()
    {
        return $this->currencyCode . '-' . $this->localeCode;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }
}
