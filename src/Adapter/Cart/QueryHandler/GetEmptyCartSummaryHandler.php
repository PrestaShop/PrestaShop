<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Cart;
use Currency;
use Order;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetEmptyCartSummary;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetEmptyCartSummaryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\EmptyCartSummary;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;

/**
 * Handles GetEmptyCartSummary query using legacy object models
 */
final class GetEmptyCartSummaryHandler extends AbstractCartHandler implements GetEmptyCartSummaryHandlerInterface
{
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @param RepositoryInterface $localeRepository
     * @param string $locale
     */
    public function __construct(
        RepositoryInterface $localeRepository,
        string $locale
    ) {
        $this->locale = $localeRepository->getLocale($locale);
    }

    /**
     * @param GetEmptyCartSummary $query
     *
     * @return EmptyCartSummary
     *
     * @throws CartNotFoundException
     * @throws LocalizationException
     */
    public function handle(GetEmptyCartSummary $query): EmptyCartSummary
    {
        $cart = $this->getContextCartObject($query->getCartId());
        $carts = $cart::getCustomerCarts($cart->id_customer, false);
        $orders = Order::getCustomerOrders($cart->id_customer);

        return new EmptyCartSummary(
            $this->decorateCarts($carts, $cart->id),
            $this->decorateOrders($orders),
            [],//@todo
            $this->getAddresses($cart)
        );
    }

    /**
     * @param array $carts
     * @param int $contextCartId
     *
     * @return array
     *
     * @throws LocalizationException
     */
    private function decorateCarts(array $carts, int $contextCartId): array
    {
        foreach ($carts as $key => &$cart) {
            if ($cart['id_cart'] == $contextCartId) {
                unset($carts[$key]);

                continue;
            }
            $currency = new Currency((int) $cart['id_currency']);
            $cart['total_price'] = $this->locale->formatPrice(
                (new Cart($cart['id_cart']))->getOrderTotal(),
                $currency->iso_code
            );
        }

        return $carts;
    }

    /**
     * @param array $orders
     *
     * @return array
     *
     * @throws LocalizationException
     */
    private function decorateOrders(array $orders): array
    {
        foreach ($orders as &$order) {
            $currency = new Currency((int) $order['id_currency']);
            $order['total_paid_real'] = $this->locale->formatPrice(
                $order['total_paid_real'],
                $currency->iso_code
            );
        }

        return $orders;
    }
}
