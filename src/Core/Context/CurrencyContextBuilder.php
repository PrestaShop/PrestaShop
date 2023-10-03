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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Currency as LegacyCurrency;
use PrestaShop\PrestaShop\Core\Model\Currency;

class CurrencyContextBuilder
{
    private ?int $currencyId = null;

    public function build(): CurrencyContext
    {
        $currency = null;

        if (!empty($this->currencyId))
        {
            $legacyCurrency = new LegacyCurrency($this->currencyId);

            $currency = new Currency(
                $legacyCurrency->id,
                $legacyCurrency->getName(),
                $legacyCurrency->getLocalizedNames(),
                $legacyCurrency->iso_code,
                $legacyCurrency->iso_code_num,
                $legacyCurrency->numeric_iso_code,
                $legacyCurrency->getConversionRate(),
                $legacyCurrency->deleted,
                $legacyCurrency->unofficial,
                $legacyCurrency->modified,
                $legacyCurrency->active,
                $legacyCurrency->getSign(),
                $legacyCurrency->getSymbol(),
                $legacyCurrency->getLocalizedSymbols(),
                $legacyCurrency->format,
                $legacyCurrency->blank,
                $legacyCurrency->decimals,
                $legacyCurrency->precision,
                $legacyCurrency->pattern,
                $legacyCurrency->getLocalizedPatterns()
            );
        }

        return new CurrencyContext($currency);
    }

    public function setCurrencyId(int $currencyId)
    {
        $this->currencyId = $currencyId;
    }
}
