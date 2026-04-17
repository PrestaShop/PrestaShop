<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order;

use Cart;
use Combination;
use Configuration;
use Currency;
use Customer;
use Group;
use Order;
use PrestaShop\PrestaShop\Adapter\Number\RoundModeConverter;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShopException;
use Product;
use Validate;

/**
 * Reusable methods for Order subdomain command/query handlers.
 */
abstract class AbstractOrderHandler
{
    /**
     * @param OrderId $orderId
     *
     * @return Order
     */
    protected function getOrder(OrderId $orderId)
    {
        try {
            $order = new Order($orderId->getValue());
        } catch (PrestaShopException $e) {
            throw new OrderException(
                sprintf(
                    'Error occured when trying to get order object #%s',
                    $orderId->getValue()
                ),
                0,
                $e
            );
        }

        if ($order->id !== $orderId->getValue()) {
            throw new OrderNotFoundException($orderId, sprintf('Order with id "%d" was not found.', $orderId->getValue()));
        }

        return $order;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    protected function isTaxIncludedInOrder(Order $order): bool
    {
        return $this->getOrderTaxCalculationMethod($order) === PS_TAX_INC;
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    protected function getOrderTaxCalculationMethod(Order $order): int
    {
        $customer = new Customer($order->id_customer);

        return Group::getPriceDisplayMethod((int) $customer->id_default_group);
    }

    /**
     * @param Cart $cart
     *
     * @return int
     */
    protected function getPrecisionFromCart(Cart $cart): int
    {
        $computingPrecision = new ComputingPrecision();
        $currency = new Currency((int) $cart->id_currency);

        return $computingPrecision->getPrecision($currency->precision);
    }

    /**
     * @param int $combinationId
     *
     * @return Combination|null
     */
    protected function getCombination(int $combinationId)
    {
        $combination = null;

        if (0 !== $combinationId) {
            $combination = new Combination($combinationId);

            if (!Validate::isLoadedObject($combination)) {
                throw new OrderException('Product combination not found.');
            }
        }

        return $combination;
    }

    /**
     * @param ProductId $productId
     * @param int $langId
     *
     * @return Product
     */
    protected function getProduct(ProductId $productId, $langId)
    {
        $product = new Product($productId->getValue(), false, $langId);

        if ($product->id !== $productId->getValue()) {
            throw new OrderException(sprintf('Product with id "%d" is invalid.', $productId->getValue()));
        }

        return $product;
    }

    /**
     * @return string
     */
    protected function getNumberRoundMode(): string
    {
        return RoundModeConverter::getNumberRoundMode((int) Configuration::get('PS_PRICE_ROUND_MODE'));
    }

    /**
     * Delivery option consists of deliveryAddress and carrierId.
     *
     * Legacy multishipping feature used comma separated carriers in delivery option (e.g. {'1':'6,7'}
     * Now that multishipping is gone - delivery option should consist of one carrier and one address.
     *
     * However the structure of deliveryOptions is still used with comma in legacy, so
     * this method provides assurance for deliveryOption structure until major refactoring
     *
     * @param int $carrierId
     *
     * @return string
     */
    protected function formatLegacyDeliveryOptionFromCarrierId(int $carrierId): string
    {
        return sprintf('%d,', $carrierId);
    }
}
