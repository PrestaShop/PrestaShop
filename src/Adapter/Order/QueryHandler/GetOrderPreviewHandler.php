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

use Carrier;
use Country;
use Currency;
use Customer;
use Order;
use OrderCarrier;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderPreviewHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewInvoiceDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewProductDetail;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewShippingDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use Validate;

/**
 * Handles GetOrderPreview query using legacy object model
 */
final class GetOrderPreviewHandler implements GetOrderPreviewHandlerInterface
{
    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param LocaleRepository $localeRepository
     * @param string $locale
     */
    public function __construct(
        LocaleRepository $localeRepository,
        string $locale
    ) {
        $this->localeRepository = $localeRepository;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderPreview $query): OrderPreview
    {
        $order = $this->getOrder($query->getOrderId());

        return new OrderPreview(
            $this->getInvoiceDetails($order),
            $this->getShippingDetails($order),
            $this->getProductDetails($order),
            $order->isVirtual(),
            PS_TAX_INC === $order->getTaxCalculationMethod()
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
     * @return OrderPreviewInvoiceDetails
     */
    private function getInvoiceDetails(Order $order): OrderPreviewInvoiceDetails
    {
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_invoice);
        $country = new Country($address->id_country);

        return new OrderPreviewInvoiceDetails(
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

    /**
     * {@inheritdoc}
     */
    private function getShippingDetails(Order $order): OrderPreviewShippingDetails
    {
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery);
        $country = new Country($address->id_country);
        $carrier = new Carrier($order->id_carrier);

        $carrierName = null;

        if (Validate::isLoadedObject($carrier)) {
            $carrierName = $carrier->name;
        }

        $orderCarrierId = $order->getIdOrderCarrier();
        $orderCarrier = new OrderCarrier($orderCarrierId);

        return new OrderPreviewShippingDetails(
            $customer->firstname,
            $customer->lastname,
            $address->address1,
            $address->address2,
            $address->city,
            $country->name[$order->id_lang],
            $address->phone,
            $carrierName,
            $orderCarrier->tracking_number ?: null
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderPreviewProductDetail[]
     */
    private function getProductDetails(Order $order): array
    {
        $productDetails = [];
        $currency = new Currency($order->id_currency);
        $locale = $this->localeRepository->getLocale($this->locale);

        foreach ($order->getProductsDetail() as $detail) {
            $unitPrice = $detail['unit_price_tax_excl'];
            $totalPrice = $detail['total_price_tax_excl'];

            if (PS_TAX_INC === $order->getTaxCalculationMethod()) {
                $unitPrice = $detail['unit_price_tax_incl'];
                $totalPrice = $detail['total_price_tax_incl'];
            }

            $productDetails[] = new OrderPreviewProductDetail(
                $detail['product_name'],
                (int) $detail['product_quantity'],
                $locale->formatPrice($unitPrice, $currency->iso_code),
                $locale->formatPrice($totalPrice, $currency->iso_code)
            );
        }

        return $productDetails;
    }
}
