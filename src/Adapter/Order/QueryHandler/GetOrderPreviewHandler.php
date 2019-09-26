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

namespace PrestaShop\PrestaShop\Adapter\Order\QueryHandler;

use Country;
use Customer;
use Order;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderPreviewHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\InvoiceDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ProductDetail;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ShippingDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Handles GetOrderPreview query using legacy object model
 */
final class GetOrderPreviewHandler implements GetOrderPreviewHandlerInterface
{
    public function handle(GetOrderPreview $query): OrderPreview
    {
        $order = $this->getOrder($query->getOrderId());

        return new OrderPreview(
            $this->getInvoiceDetails($order),
            $this->getShippingDetails($order),
            $this->getProductDetails($order)
        );
    }

    /**
     * @param OrderId $orderId
     *
     * @return Order
     *
     * @throws OrderNotFoundException
     */
    private function getOrder(OrderId $orderId): Order
    {
        $order = new Order($orderId->getValue());
        if ($order->id !== $orderId->getValue()) {
            throw new OrderNotFoundException(
                $orderId,
                sprintf('Order with id "%s" was not found.', $orderId->getValue())
            );
        }

        return $order;
    }

    /**
     * @param Order $order
     *
     * @return InvoiceDetails
     */
    private function getInvoiceDetails(Order $order): InvoiceDetails
    {
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_invoice);
        $country = new Country($address->id_country);

        return new InvoiceDetails(
            $customer->firstname,
            $customer->lastname,
            $address->address1,
            $address->address2,
            $address->city,
            $country->name[$order->id_lang],
            $customer->email,
            $address->phone,
            $address->company
        );
    }

    private function getShippingDetails(Order $order): ShippingDetails
    {
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery);
        $country = new Country($address->id_country);

        return new ShippingDetails(
            $customer->firstname,
            $customer->lastname,
            $address->address1,
            $address->address2,
            $address->city,
            $country->name[$order->id_lang],
            $address->phone
        );
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getProductDetails(Order $order): array
    {
        $productDetails = [];

        foreach ($order->getProductsDetail() as $detail) {
            $priceTaxIncl = new Number($detail['total_price_tax_incl']);
            $priceTaxExcl = new Number($detail['total_price_tax_excl']);

            $totalTaxes = $priceTaxIncl->minus($priceTaxExcl);

            $productDetails[] = new ProductDetail(
                $detail['product_name'],
                (int) $detail['product_quantity'],
                $totalTaxes,
                $priceTaxIncl
            );
        }

        return $productDetails;
    }
}
