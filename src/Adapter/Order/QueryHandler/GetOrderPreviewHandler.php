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

namespace PrestaShop\PrestaShop\Adapter\Order\QueryHandler;

use Carrier;
use Country;
use Currency;
use Customer;
use Group;
use Order;
use OrderCarrier;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Address\AddressFormatter;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Core\Address\AddressFormatterInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderPreviewHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewInvoiceDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewProductDetail;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewShippingDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShop\PrestaShop\Core\Util\Sorter;
use State;
use StockAvailable;
use Validate;

/**
 * Handles GetOrderPreview query using legacy object model
 */
#[AsQueryHandler]
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
     * @var AddressFormatterInterface
     */
    private $addressFormatter;

    /**
     * @param LocaleRepository $localeRepository
     * @param string $locale
     * @param AddressFormatterInterface|null $addressFormatter
     */
    public function __construct(
        LocaleRepository $localeRepository,
        string $locale,
        AddressFormatterInterface $addressFormatter = null
    ) {
        $this->localeRepository = $localeRepository;
        $this->locale = $locale;
        $this->addressFormatter = $addressFormatter ?? new AddressFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderPreview $query): OrderPreview
    {
        $order = $this->getOrder($query->getOrderId());
        $priceDisplayMethod = $this->getOrderTaxCalculationMethod($order);

        return new OrderPreview(
            $this->getInvoiceDetails($order),
            $this->getShippingDetails($order),
            $this->getProductDetails($order),
            $order->isVirtual(),
            $priceDisplayMethod == PS_TAX_INC,
            $this->addressFormatter->format(new AddressId((int) $order->id_address_invoice)),
            $this->addressFormatter->format(new AddressId((int) $order->id_address_delivery))
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
            throw new OrderNotFoundException($orderId, sprintf('Order with id "%s" was not found.', $orderId->getValue()));
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
        $state = new State($address->id_state);
        $stateName = Validate::isLoadedObject($state) ? $state->name : null;
        $dni = Address::dniRequired($address->id_country) ? $address->dni : null;

        return new OrderPreviewInvoiceDetails(
            $address->firstname,
            $address->lastname,
            $address->company,
            $address->vat_number,
            $address->address1,
            $address->address2,
            $address->city,
            $address->postcode,
            $stateName,
            $country->name[(int) $order->getAssociatedLanguage()->getId()],
            $customer->email,
            $address->phone,
            $dni
        );
    }

    /**
     * {@inheritdoc}
     */
    private function getShippingDetails(Order $order): OrderPreviewShippingDetails
    {
        $address = new Address($order->id_address_delivery);
        $country = new Country($address->id_country);
        $carrier = new Carrier($order->id_carrier);
        $state = new State($address->id_state);

        $carrierName = $trackingUrl = null;
        $stateName = Validate::isLoadedObject($state) ? $state->name : null;

        $orderCarrierId = $order->getIdOrderCarrier();
        $orderCarrier = new OrderCarrier($orderCarrierId);

        if (Validate::isLoadedObject($carrier)) {
            $carrierName = $carrier->name;
            $trackingUrl = str_replace('@', $orderCarrier->tracking_number ?: '@', $carrier->url);
        }

        $dni = Address::dniRequired($address->id_country) ? $address->dni : null;

        return new OrderPreviewShippingDetails(
            $address->firstname,
            $address->lastname,
            $address->company,
            $address->vat_number,
            $address->address1,
            $address->address2,
            $address->city,
            $address->postcode,
            $stateName,
            $country->name[(int) $order->getAssociatedLanguage()->getId()],
            $address->phone,
            $carrierName,
            $orderCarrier->tracking_number ?: null,
            $dni,
            $trackingUrl
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

        $taxCalculationMethod = $this->getOrderTaxCalculationMethod($order);

        $orderDetails = $order->getProductsDetail();
        // Sort products by Reference ID (and if equals (like combination) by Supplier Reference)
        $sorter = new Sorter();
        $orderDetails = $sorter->natural($orderDetails, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');

        foreach ($orderDetails as $detail) {
            $unitPrice = $detail['unit_price_tax_excl'];
            $totalPrice = $detail['total_price_tax_excl'];

            $totalPriceTaxIncl = new DecimalNumber($detail['total_price_tax_incl']);
            $totalPriceTaxExcl = new DecimalNumber($detail['total_price_tax_excl']);

            $totalTaxAmount = $totalPriceTaxIncl->minus($totalPriceTaxExcl);

            if (PS_TAX_INC === $taxCalculationMethod) {
                $unitPrice = $detail['unit_price_tax_incl'];
                $totalPrice = $detail['total_price_tax_incl'];
            }

            $productDetails[] = new OrderPreviewProductDetail(
                $detail['product_name'],
                $detail['product_reference'],
                StockAvailable::getLocation(
                    $detail['product_id'],
                    $detail['product_attribute_id'],
                    $detail['id_shop']
                ),
                (int) $detail['product_quantity'],
                $locale->formatPrice($unitPrice, $currency->iso_code),
                $locale->formatPrice($totalPrice, $currency->iso_code),
                $locale->formatPrice((string) $totalTaxAmount, $currency->iso_code),
                (int) $detail['id_product']
            );
        }

        return $productDetails;
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    private function getOrderTaxCalculationMethod(Order $order): int
    {
        $customer = new Customer($order->id_customer);

        return Group::getPriceDisplayMethod((int) $customer->id_default_group);
    }
}
