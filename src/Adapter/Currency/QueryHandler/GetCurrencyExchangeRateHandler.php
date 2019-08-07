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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\ExchangeRateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetCurrencyExchangeRateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ExchangeRate;
use Currency;
use Tools;

class GetCurrencyExchangeRateHandler implements GetCurrencyExchangeRateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCurrencyExchangeRate $query)
    {
        if (!($feed = Tools::simplexml_load_file(_PS_CURRENCY_FEED_URL_)) || !$feed->list) {
            throw new ExchangeRateNotFoundException('Cannot fetch exchange rate feed');
        }
        $data = $feed->list;

        // Default feed currency (usually EUR)
        $sourceIsoCode = (string) ($feed->source['iso_code']);

        if (!$defaultCurrency = Currency::getDefaultCurrency()) {
            throw new CurrencyNotFoundException('Default Currency object was not found');
        }
        if ($defaultCurrency->iso_code == $query->getIsoCode()->getValue()) {
            return new ExchangeRate(ExchangeRate::DEFAULT_RATE);
        }

        // Fetch the exchange rate of the default currency (compared to the source currency)
        $defaultExchangeRate = 1;
        if ($defaultCurrency->iso_code != $sourceIsoCode) {
            foreach ($data->currency as $currency) {
                if ($currency['iso_code'] == $defaultCurrency->iso_code) {
                    $defaultExchangeRate = round((float) $currency['rate'], 6);

                    break;
                }
            }
        }

        if ($sourceIsoCode == $query->getIsoCode()->getValue()) {
            return new ExchangeRate(ExchangeRate::DEFAULT_RATE);
        } else {
            foreach ($data->currency as $obj) {
                if ((string) ($obj['iso_code']) == $query->getIsoCode()->getValue()) {
                    $rate = (float) $obj['rate'];

                    return new ExchangeRate(round($rate / $defaultExchangeRate, 6));
                }
            }
        }

        throw new ExchangeRateNotFoundException(sprintf(
            'Exchange rate for Currency with iso code %s was not found',
            $query->getIsoCode()->getValue()
        ));
    }
}
