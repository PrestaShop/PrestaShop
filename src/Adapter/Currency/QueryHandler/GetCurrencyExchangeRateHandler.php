<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
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
#[AsQueryHandler]
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
