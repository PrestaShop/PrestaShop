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

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException;
use PrestaShop\PrestaShop\Core\Currency\ExchangeRateProvider;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\ExchangeRateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetCurrencyExchangeRateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ExchangeRate;

/**
 * Class GetCurrencyExchangeRateHandler handles the GetCurrencyExchangeRate query
 * and returns the exchange rate of a specified currency via a ExchangeRate value object.
 */
class GetCurrencyExchangeRateHandler implements GetCurrencyExchangeRateHandlerInterface
{
    /** @var ExchangeRateProvider */
    private $exchangeRateProvider;

    /**
     * @param ExchangeRateProvider $exchangeRateProvider
     */
    public function __construct(ExchangeRateProvider $exchangeRateProvider)
    {
        $this->exchangeRateProvider = $exchangeRateProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCurrencyExchangeRate $query)
    {
        try {
            $currencyExchangeRate = $this->exchangeRateProvider->getExchangeRate($query->getIsoCode()->getValue());
        } catch (CurrencyFeedException $e) {
            throw new ExchangeRateNotFoundException(sprintf('Exchange rate for Currency with iso code %s was not found', $query->getIsoCode()->getValue()), 0, $e);
        }

        return new ExchangeRate($currencyExchangeRate);
    }
}
