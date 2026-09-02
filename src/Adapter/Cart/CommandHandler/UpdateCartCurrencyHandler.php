<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShopException;

/**
 * @internal
 */
#[AsCommandHandler]
final class UpdateCartCurrencyHandler extends AbstractCartHandler implements UpdateCartCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartCurrencyCommand $command): void
    {
        $currency = $this->getCurrencyObject($command->getNewCurrencyId());

        $this->assertCurrencyIsNotDeleted($currency);
        $this->assertCurrencyIsActive($currency);

        $cart = $this->getCart($command->getCartId());
        $cart->id_currency = (int) $currency->id;

        try {
            if (false === $cart->update()) {
                throw new CartException('Failed to update cart currency.');
            }
        } catch (PrestaShopException) {
            throw new CartException(sprintf('An error occurred while trying to update currency for cart with id "%s"', $cart->id));
        }
    }

    /**
     * @param CurrencyId $currencyId
     *
     * @return Currency
     *
     * @throws CurrencyNotFoundException
     */
    private function getCurrencyObject(CurrencyId $currencyId): Currency
    {
        $currency = new Currency($currencyId->getValue());

        if ($currencyId->getValue() !== $currency->id) {
            throw new CurrencyNotFoundException(sprintf('Currency with id "%s" was not found', $currencyId->getValue()));
        }

        return $currency;
    }

    /**
     * @param Currency $currency
     *
     * @throws CurrencyException
     */
    private function assertCurrencyIsActive(Currency $currency): void
    {
        if (!$currency->active) {
            throw new CurrencyException(sprintf('Currency "%s" cannot be used in cart because it is disabled', $currency->iso_code), CurrencyException::IS_DISABLED);
        }
    }

    /**
     * @param Currency $currency
     *
     * @throws CurrencyException
     */
    private function assertCurrencyIsNotDeleted(Currency $currency): void
    {
        if ($currency->deleted) {
            throw new CurrencyException(sprintf('Currency "%s" cannot be used in cart because it is deleted', $currency->iso_code), CurrencyException::IS_DELETED);
        }
    }
}
