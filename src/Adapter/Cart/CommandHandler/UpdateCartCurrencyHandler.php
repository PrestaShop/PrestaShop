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
        } catch (PrestaShopException $e) {
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
