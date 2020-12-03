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

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Cart;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Adapter\Customer\CommandHandler\AbstractCustomerHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerCarts;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerCartsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\CartSummary;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

/**
 * Handles GetCustomerCartsQuery using legacy object models
 */
final class GetCustomerCartsHandler extends AbstractCustomerHandler implements GetCustomerCartsHandlerInterface
{
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @param LocaleInterface $locale
     */
    public function __construct(
        LocaleInterface $locale
    ) {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerCarts $query): array
    {
        $customerId = $query->getCustomerId();
        $this->assertCustomerWasFound($customerId, new Customer($customerId->getValue()));

        return $this->getCarts($customerId->getValue());
    }

    /**
     * @param int $customerId
     *
     * @return array
     *
     * @throws LocalizationException
     */
    private function getCarts(int $customerId): array
    {
        $carts = Cart::getCustomerCarts($customerId, false);
        $summarizedCarts = [];

        foreach ($carts as $key => $customerCart) {
            $cartId = (int) $customerCart['id_cart'];
            $currency = new Currency((int) $customerCart['id_currency']);
            $cart = new Cart($cartId);

            $summarizedCarts[] = new CartSummary(
                $cart->id,
                $cart->date_add,
                $this->locale->formatPrice($cart->getOrderTotal(), $currency->iso_code)
            );
        }

        return $summarizedCarts;
    }
}
