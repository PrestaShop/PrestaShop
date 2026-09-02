<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\RefreshExchangeRatesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\RefreshExchangeRatesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotRefreshExchangeRatesException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;

/**
 * Class RefreshExchangeRatesHandler is responsible for refreshing currency exchange rates.
 *
 * @internal
 */
#[AsCommandHandler]
final class RefreshExchangeRatesHandler implements RefreshExchangeRatesHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(RefreshExchangeRatesCommand $command)
    {
        $error = Currency::refreshCurrencies();

        if ($error) {
            throw new CannotRefreshExchangeRatesException($error);
        }
    }
}
