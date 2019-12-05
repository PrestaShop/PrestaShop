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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
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
final class UpdateCartCurrencyHandler extends AbstractCartHandler implements UpdateCartCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartCurrencyCommand $command)
    {
        $currency = $this->getCurrencyObject($command->getNewCurrencyId());

        $this->assertCurrencyCantBeUsedInCart($currency);

        $cart = $this->getCart($command->getCartId());
        $cart->id_currency = (int) $currency->id;

        try {
            if (false === $cart->update()) {
                throw new CartException('Failed to update cart currency.');
            }
        } catch (PrestaShopException $e) {
            throw new CartException(sprintf(
                'An error occurred while trying to update currency for cart with id "%s"',
                $cart->id
            ));
        }
    }

    /**
     * @param CurrencyId $currencyId
     *
     * @return Currency
     *
     * @throws CurrencyNotFoundException
     */
    private function getCurrencyObject(CurrencyId $currencyId)
    {
        $currency = new Currency($currencyId->getValue());

        if ($currencyId->getValue() !== $currency->id) {
            throw new CurrencyNotFoundException(
                sprintf('Currency with id "%s" was not found', $currencyId->getValue())
            );
        }

        return $currency;
    }

    /**
     * @param Currency $currency
     */
    private function assertCurrencyCantBeUsedInCart(Currency $currency)
    {
        if ($currency->deleted || !$currency->active) {
            throw new CurrencyException(sprintf(
                'Currency "%s" cannot be used in cart because it is either deleted or disabled',
                $currency->iso_code
            ));
        }
    }
}
