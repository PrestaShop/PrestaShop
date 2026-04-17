<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ExchangeRate;

/**
 * Interface GetCurrencyExchangeRateHandlerInterface defines contract for GetCurrencyExchangeRateHandler.
 */
interface GetCurrencyExchangeRateHandlerInterface
{
    /**
     * @param GetCurrencyExchangeRate $query
     *
     * @return ExchangeRate
     */
    public function handle(GetCurrencyExchangeRate $query);
}
