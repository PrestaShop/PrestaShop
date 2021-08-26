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

use Address;
use Carrier;
use Cart;
use ConnectionsSource;
use Context;
use Country;
use Currency;
use Customer;
use DateTimeImmutable;
use Gender;
use Module;
use Order;
use OrderInvoice;
use OrderPayment;
use OrderSlip;
use OrderState;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Address\AddressFormatter;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Address\AddressFormatterInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Exception\InvalidSortingException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderDocumentType;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderProductsForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\LinkedOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\LinkedOrdersForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCarrierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDiscountForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDiscountsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDocumentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDocumentsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderHistoryForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderInvoiceAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderMessageDateForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderMessageForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderMessagesForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPricesForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderReturnForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderReturnsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderSourceForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderSourcesForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderStatusForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use State;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Handle getting order for viewing
 *
 * @internal
 */
final class GetOrderForViewingHandler extends AbstractOrderHandler implements GetOrderForViewingHandlerInterface
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var CustomerDataProvider
     */
    private $customerDataProvider;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var GetOrderProductsForViewingHandlerInterface
     */
    private $getOrderProductsForViewingHandler;

    /**
     * @var AddressFormatterInterface
     */
    private $addressFormatter;

    /**
     * @param TranslatorInterface $translator
     * @param int $contextLanguageId
     * @param Locale $locale
     * @param Context $context
     * @param CustomerDataProvider $customerDataProvider
     * @param GetOrderProductsForViewingHandlerInterface $getOrderProductsForViewingHandler
     */
    public function __construct(
        TranslatorInterface $translator,
        int $contextLanguageId,
        Locale $locale,
        Context $context,
        CustomerDataProvider $customerDataProvider,
        GetOrderProductsForViewingHandlerInterface $getOrderProductsForViewingHandler,
        Configuration $configuration,
        AddressFormatterInterface $addressFormatter = null
    ) {
        $this->translator = $translator;
        $this->contextLanguageId = $contextLanguageId;
        $this->locale = $locale;
        $this->translator = $translator;
        $this->context = $context;
        $this->customerDataProvider = $customerDataProvider;
        $this->getOrderProductsForViewingHandler = $getOrderProductsForViewingHandler;
        $this->configuration = $configuration;
        $this->addressFormatter = $addressFormatter ?? new AddressFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderForViewing $query): OrderForViewing
    {
        $order = $this->getOrder($query->getOrderId());
        $orderCarrier = new Carrier($order->id_carrier);
        $taxCalculationMethod = $this->getOrderTaxCalculationMethod($order);

        $isTaxIncluded = ($taxCalculationMethod == PS_TAX_INC);

        $taxMethod = $isTaxIncluded ?
            $this->translator->trans('Tax included', [], 'Admin.Global') :
            $this->translator->trans('Tax excluded', [], 'Admin.Global');

        $invoiceManagementIsEnabled = (bool) $this->configuration->get(
            'PS_INVOICE',
            null,
            ShopConstraint::shop((int) $order->id_shop)
        );

        $orderInvoiceAddress = $this->getOrderInvoiceAddress($order);

        return new OrderForViewing(
            (int) $order->id,
            (int) $order->id_currency,
            (int) $order->id_carrier,
            (string) $orderCarrier->name,
            (int) $order->id_shop,
            $order->reference,
            (bool) $order->isVirtual(),
            $taxMethod,
            $isTaxIncluded,
            (bool) $order->valid,
            $order->hasBeenPaid(),
            $order->hasInvoice(),
            $order->hasBeenDelivered(),
            $order->hasBeenShipped(),
            $invoiceManagementIsEnabled,
            new DateTimeImmutable($order->date_add),
            $this->getOrderCustomer($order, $orderInvoiceAddress),
            $this->getOrderShippingAddress($order),
            $orderInvoiceAddress,
            $this->getOrderProducts($query->getOrderId(), $query->getProductsSorting()->getValue()),
            $this->getOrderHistory($order),
            $this->getOrderDocuments($order),
            $this->getOrderShipping($order),
            $this->getOrderReturns($order),
            $this->getOrderPayments($order),
            $this->getOrderMessages($order),
            $this->getOrderPrices($order),
            $this->getOrderDiscounts($order),
            $this->getOrderSources($order),
            $this->getLinkedOrders($order),
            $this->addressFormatter->format(new AddressId((int) $order->id_address_delivery)),
            $this->addressFormatter->format(new AddressId((int) $order->id_address_invoice)),
            (string) $order->note
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderCustomerForViewing
     */
    private function getOrderCustomer(Order $order, OrderInvoiceAddressForViewing $invoiceAddress): OrderCustomerForViewing
    {
        $currency = new Currency($order->id_currency);
        $customer = new Customer($order->id_customer);
        $genderName = '';
        $totalSpentSinceRegistration = null;

        if (!Validate::isLoadedObject($customer)) {
            $customer = $this->buildFakeCustomerObject($order, $invoiceAddress);
            $customerStats = ['nb_orders' => 1]; // Count this current order as loaded
        } else {
            $gender = new Gender($customer->id_gender);
            if (Validate::isLoadedObject($gender)) {
                $genderName = $gender->name[(int) $order->getAssociatedLanguage()->getId()];
            }

            $customerStats = $customer->getStats();
            $totalSpentSinceRegistration = Tools::convertPrice($customerStats['total_orders'], $order->id_currency);
        }

        $isB2BEnabled = $this->configuration->getBoolean('PS_B2B_ENABLE');

        return new OrderCustomerForViewing(
            (int) $customer->id,
            $customer->firstname,
            $customer->lastname,
            $genderName,
            $customer->email,
            new DateTimeImmutable($customer->date_add),
            $totalSpentSinceRegistration !== null ? $this->locale->formatPrice($totalSpentSinceRegistration, $currency->iso_code) : '',
            $customerStats['nb_orders'],
            $customer->note,
            (bool) $customer->is_guest,
            (int) $order->getAssociatedLanguage()->getId(),
            $isB2BEnabled ? ($customer->ape ?: '') : '',
            $isB2BEnabled ? ($customer->siret ?: '') : ''
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderShippingAddressForViewing
     */
    public function getOrderShippingAddress(Order $order): OrderShippingAddressForViewing
    {
        $address = new Address($order->id_address_delivery);
        $country = new Country($address->id_country);
        $stateName = '';

        if ($address->id_state) {
            $state = new State($address->id_state);

            $stateName = $state->name;
        }

        $dni = Address::dniRequired($address->id_country) ? $address->dni : null;

        return new OrderShippingAddressForViewing(
            $address->id,
            $address->firstname,
            $address->lastname,
            $address->company,
            $address->address1,
            $address->address2,
            $stateName,
            $address->city,
            $country->name[(int) $order->getAssociatedLanguage()->getId()],
            $address->postcode,
            $address->phone,
            $address->phone_mobile,
            $address->vat_number,
            $dni
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderInvoiceAddressForViewing
     */
    private function getOrderInvoiceAddress(Order $order): OrderInvoiceAddressForViewing
    {
        $address = new Address($order->id_address_invoice);
        $country = new Country($address->id_country);
        $stateName = '';

        if ($address->id_state) {
            $state = new State($address->id_state);

            $stateName = $state->name;
        }

        $dni = Address::dniRequired($address->id_country) ? $address->dni : null;

        return new OrderInvoiceAddressForViewing(
            $address->id,
            $address->firstname,
            $address->lastname,
            $address->company,
            $address->address1,
            $address->address2,
            $stateName,
            $address->city,
            $country->name[(int) $order->getAssociatedLanguage()->getId()],
            $address->postcode,
            $address->phone,
            $address->phone_mobile,
            $address->vat_number,
            $dni
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderHistoryForViewing
     */
    private function getOrderHistory(Order $order): OrderHistoryForViewing
    {
        $history = $order->getHistory($this->contextLanguageId);

        $statuses = [];

        foreach ($history as $item) {
            $statuses[] = new OrderStatusForViewing(
                (int) $item['id_order_history'],
                (int) $item['id_order_state'],
                $item['ostate_name'],
                $item['color'],
                new DateTimeImmutable($item['date_add']),
                (bool) $item['send_email'],
                $item['employee_firstname'],
                $item['employee_lastname']
            );
        }

        return new OrderHistoryForViewing(
            $order->current_state,
            $statuses
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderDocumentsForViewing
     *
     * @throws LocalizationException
     */
    private function getOrderDocuments(Order $order): OrderDocumentsForViewing
    {
        $currency = new Currency($order->id_currency);
        $documents = $order->getDocuments();

        $documentsForViewing = [];

        /** @var OrderInvoice|OrderSlip $document */
        foreach ($documents as $document) {
            $type = null;
            $number = null;
            $amount = null;
            $numericAmount = null;
            $amountMismatch = null;
            $isAddPaymentAllowed = false;

            if ($document instanceof OrderInvoice) {
                $type = isset($document->is_delivery) ? OrderDocumentType::DELIVERY_SLIP : OrderDocumentType::INVOICE;
            } elseif ($document instanceof OrderSlip) {
                $type = OrderDocumentType::CREDIT_SLIP;
            }

            if (OrderDocumentType::INVOICE === $type) {
                $number = $document->getInvoiceNumberFormatted(
                    $this->contextLanguageId,
                    $order->id_shop
                );

                if ($document->getRestPaid()) {
                    $isAddPaymentAllowed = true;
                }
                $amount = $this->locale->formatPrice($document->total_paid_tax_incl, $currency->iso_code);
                $numericAmount = $document->total_paid_tax_incl;

                if ($document->getTotalPaid()) {
                    if ($document->getRestPaid() > 0) {
                        $amountMismatch = sprintf(
                            '%s %s',
                            $this->locale->formatPrice($document->getRestPaid(), $currency->iso_code),
                            $this->translator->trans('not paid', [], 'Admin.Orderscustomers.Feature')
                        );
                    } elseif ($document->getRestPaid() < 0) {
                        $amountMismatch = sprintf(
                            '%s %s',
                            $this->locale->formatPrice($document->getRestPaid(), $currency->iso_code),
                            $this->translator->trans('overpaid', [], 'Admin.Orderscustomers.Feature')
                        );
                    }
                }
            } elseif (OrderDocumentType::DELIVERY_SLIP === $type) {
                $conf = $this->configuration->get(
                    'PS_DELIVERY_PREFIX',
                    null,
                    ShopConstraint::shop((int) $order->id_shop)
                );
                $number = sprintf(
                    '%s%06d',
                    $conf[$this->contextLanguageId] ?? '',
                    $document->delivery_number
                );
                $amount = $this->locale->formatPrice(
                    $document->total_paid_tax_incl,
                    $currency->iso_code
                );
                $numericAmount = $document->total_paid_tax_incl;
            } elseif (OrderDocumentType::CREDIT_SLIP === $type) {
                $conf = $this->configuration->get('PS_CREDIT_SLIP_PREFIX');
                $number = sprintf(
                    '%s%06d',
                    $conf[$this->contextLanguageId] ?? '',
                    $document->id
                );
                $amount = $this->locale->formatPrice(
                    $document->total_products_tax_incl + $document->total_shipping_tax_incl,
                    $currency->iso_code
                );
                $numericAmount = $document->total_products_tax_incl + $document->total_shipping_tax_incl;
            }

            $documentsForViewing[] = new OrderDocumentForViewing(
                $document->id,
                $type,
                new DateTimeImmutable($document->date_add),
                $number,
                $numericAmount,
                $amount,
                $amountMismatch,
                $document instanceof OrderInvoice ? $document->note : null,
                $isAddPaymentAllowed
            );
        }

        $canGenerateInvoice = $this->configuration->get('PS_INVOICE') &&
            count($order->getInvoicesCollection()) &&
            $order->invoice_number;

        $canGenerateDeliverySlip = (bool) $order->delivery_number;

        return new OrderDocumentsForViewing(
            $canGenerateInvoice,
            $canGenerateDeliverySlip,
            $documentsForViewing
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderShippingForViewing
     *
     * @throws LocalizationException
     */
    private function getOrderShipping(Order $order): OrderShippingForViewing
    {
        $taxCalculationMethod = $this->getOrderTaxCalculationMethod($order);

        $shipping = $order->getShipping();
        $carriers = [];
        $carrierModuleInfo = null;

        $currency = new Currency($order->id_currency);
        $carrier = new Carrier($order->id_carrier);
        $carrierModuleInfo = null;

        if ($carrier->is_module) {
            $module = Module::getInstanceByName($carrier->external_module_name);
            if (method_exists($module, 'displayInfoByCart')) {
                $carrierModuleInfo = $module->displayInfoByCart($order->id_cart);
            }
        }

        if (!$order->isVirtual()) {
            foreach ($shipping as $item) {
                if ($taxCalculationMethod == PS_TAX_INC) {
                    $price = Tools::displayPrice($item['shipping_cost_tax_incl'], $currency);
                } else {
                    $price = Tools::displayPrice($item['shipping_cost_tax_excl'], $currency);
                }

                $trackingUrl = null;
                $trackingNumber = $item['tracking_number'];

                if ($item['url'] && $trackingNumber) {
                    $trackingUrl = str_replace('@', $trackingNumber, $item['url']);
                }

                $weight = sprintf('%.3f %s', $item['weight'], $this->configuration->get('PS_WEIGHT_UNIT'));

                $carriers[] = new OrderCarrierForViewing(
                    (int) $item['id_order_carrier'],
                    new DateTimeImmutable($item['date_add']),
                    $item['carrier_name'],
                    $weight,
                    (int) $item['id_carrier'],
                    $price,
                    $trackingUrl,
                    $trackingNumber,
                    $item['can_edit']
                );
            }
        }

        return new OrderShippingForViewing(
            $carriers,
            (bool) $order->recyclable,
            (bool) $order->gift,
            $order->gift_message,
            $carrierModuleInfo
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderReturnsForViewing
     */
    private function getOrderReturns(Order $order): OrderReturnsForViewing
    {
        $returns = $order->getReturn();

        if ($order->isVirtual()) {
            return new OrderReturnsForViewing();
        }

        $orderReturns = [];

        foreach ($returns as $orderReturn) {
            $trackingUrl = null;
            $trackingNumber = null;

            if (isset($orderReturn['url'], $orderReturn['tracking_number'])) {
                $trackingUrl = $orderReturn['url'];
                $trackingNumber = $orderReturn['tracking_number'];
            } elseif (isset($orderReturn['tracking_number'])) {
                $trackingNumber = $orderReturn['tracking_number'];
            }

            $orderReturns[] = new OrderReturnForViewing(
                (int) $orderReturn['id_order_return'],
                isset($orderReturn['id_order_invoice']) ? (int) $orderReturn['id_order_invoice'] : 0,
                isset($orderReturn['id_carrier']) ? (int) $orderReturn['id_carrier'] : 0,
                new DateTimeImmutable($orderReturn['date_add']),
                $orderReturn['type'],
                $orderReturn['state_name'],
                $trackingUrl,
                $trackingNumber
            );
        }

        return new OrderReturnsForViewing($orderReturns);
    }

    /**
     * @param Order $order
     *
     * @return OrderPaymentsForViewing
     *
     * @throws LocalizationException
     */
    private function getOrderPayments(Order $order): OrderPaymentsForViewing
    {
        $currency = new Currency($order->id_currency);
        $payments = $order->getOrderPayments();

        $currentState = $order->getCurrentOrderState();

        $orderAmountToPay = null;
        $orderAmountPaid = null;
        $paymentMismatchOrders = [];

        if (count($payments) > 0) {
            $noPaymentMismatch = round($order->getOrdersTotalPaid(), 2) == round($order->getTotalPaid(), 2)
                || ($currentState && $currentState->id == 6);

            if (!$noPaymentMismatch) {
                $orderAmountToPay = $this->locale->formatPrice($order->getOrdersTotalPaid(), $currency->iso_code);
                $orderAmountPaid = $this->locale->formatPrice($order->getTotalPaid(), $currency->iso_code);

                foreach ($order->getBrother() as $relatedOrder) {
                    $paymentMismatchOrders[] = $relatedOrder->id;
                }
            }
        }

        $orderPayments = [];

        /** @var OrderPayment $payment */
        foreach ($order->getOrderPaymentCollection() as $payment) {
            $currency = new Currency($payment->id_currency);
            $invoice = $payment->getOrderInvoice($order->id);
            $invoiceNumber = $invoice ?
                $invoice->getInvoiceNumberFormatted($this->contextLanguageId, $order->id_shop) :
                null;

            $orderPayments[] = new OrderPaymentForViewing(
                $payment->id,
                new DateTimeImmutable($payment->date_add),
                $payment->payment_method,
                $payment->transaction_id,
                $this->locale->formatPrice($payment->amount, $currency->iso_code),
                $invoiceNumber,
                $payment->card_number,
                $payment->card_brand,
                $payment->card_expiration,
                $payment->card_holder
            );
        }

        return new OrderPaymentsForViewing(
            $orderPayments,
            $orderAmountToPay,
            $orderAmountPaid,
            $paymentMismatchOrders
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderMessagesForViewing
     */
    private function getOrderMessages(Order $order): OrderMessagesForViewing
    {
        $orderMessagesForOrderPage = $this->customerDataProvider->getCustomerMessages(
            (int) $order->id_customer,
            (int) $order->id
        );

        $messages = [];

        foreach ($orderMessagesForOrderPage['messages'] as $orderMessage) {
            $messageEmployeeId = (int) $orderMessage['id_employee'];
            $isCurrentEmployeesMessage = (int) $this->context->employee->id === $messageEmployeeId;

            $messages[] = new OrderMessageForViewing(
                (int) $orderMessage['id_customer_message'],
                $orderMessage['message'],
                new OrderMessageDateForViewing(
                    new DateTimeImmutable($orderMessage['date_add']),
                    $this->context->language->date_format_full
                ),
                $messageEmployeeId,
                $isCurrentEmployeesMessage,
                $orderMessage['efirstname'],
                $orderMessage['elastname'],
                $orderMessage['cfirstname'],
                $orderMessage['clastname'],
                (bool) $orderMessage['private']
            );
        }

        return new OrderMessagesForViewing($messages, $orderMessagesForOrderPage['total']);
    }

    /**
     * @param Order $order
     *
     * @return OrderPricesForViewing
     *
     * @throws LocalizationException
     */
    private function getOrderPrices(Order $order): OrderPricesForViewing
    {
        $currency = new Currency($order->id_currency);
        $customer = $order->getCustomer();

        $isTaxExcluded = !$this->isTaxIncludedInOrder($order);

        $shipping_refundable_tax_excl = $order->total_shipping_tax_excl;
        $shipping_refundable_tax_incl = $order->total_shipping_tax_incl;

        $slips = OrderSlip::getOrdersSlip($customer->id, $order->id);
        foreach ($slips as $slip) {
            $shipping_refundable_tax_excl -= $slip['total_shipping_tax_excl'];
            $shipping_refundable_tax_incl -= $slip['total_shipping_tax_incl'];
        }

        if ($isTaxExcluded) {
            $productsPrice = (float) $order->total_products;
            $discountsAmount = (float) $order->total_discounts_tax_excl;
            $wrappingPrice = (float) $order->total_wrapping_tax_excl;
            $shippingPrice = (float) $order->total_shipping_tax_excl;
            $shippingRefundable = max(0, $shipping_refundable_tax_excl);
        } else {
            $productsPrice = (float) $order->total_products_wt;
            $discountsAmount = (float) $order->total_discounts_tax_incl;
            $wrappingPrice = (float) $order->total_wrapping_tax_incl;
            $shippingPrice = (float) $order->total_shipping_tax_incl;
            $shippingRefundable = max(0, $shipping_refundable_tax_incl);
        }
        $totalAmount = (float) $order->total_paid_tax_incl;

        $taxesAmount = $order->total_paid_tax_incl - $order->total_paid_tax_excl;

        return new OrderPricesForViewing(
            new DecimalNumber((string) $productsPrice),
            new DecimalNumber((string) $discountsAmount),
            new DecimalNumber((string) $wrappingPrice),
            new DecimalNumber((string) $shippingPrice),
            new DecimalNumber((string) $shippingRefundable),
            new DecimalNumber((string) $taxesAmount),
            new DecimalNumber((string) $totalAmount),
            Tools::displayPrice($productsPrice, $currency),
            Tools::displayPrice($discountsAmount, $currency),
            Tools::displayPrice($wrappingPrice, $currency),
            Tools::displayPrice($shippingPrice, $currency),
            Tools::displayPrice($shippingRefundable, $currency),
            Tools::displayPrice($taxesAmount, $currency),
            Tools::displayPrice($totalAmount, $currency)
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderDiscountsForViewing
     *
     * @throws LocalizationException
     */
    private function getOrderDiscounts(Order $order): OrderDiscountsForViewing
    {
        $isTaxIncluded = $this->isTaxIncludedInOrder($order);
        $currency = new Currency($order->id_currency);
        $discounts = $order->getCartRules();
        $discountsForViewing = [];

        foreach ($discounts as $discount) {
            $discountAmount = $isTaxIncluded ? $discount['value'] : $discount['value_tax_excl'];
            $discountsForViewing[] = new OrderDiscountForViewing(
                (int) $discount['id_order_cart_rule'],
                $discount['name'],
                new DecimalNumber((string) $discountAmount),
                Tools::displayPrice($discountAmount, $currency)
            );
        }

        return new OrderDiscountsForViewing($discountsForViewing);
    }

    /**
     * @param Order $order
     *
     * @return OrderSourcesForViewing
     */
    private function getOrderSources(Order $order): OrderSourcesForViewing
    {
        $sourcesData = ConnectionsSource::getOrderSources($order->id);
        $sources = [];

        foreach ($sourcesData as $sourceItem) {
            $sources[] = new OrderSourceForViewing(
                $sourceItem['http_referer'],
                $sourceItem['request_uri'],
                new DateTimeImmutable($sourceItem['date_add']),
                $sourceItem['keywords']
            );
        }

        return new OrderSourcesForViewing($sources);
    }

    /**
     * @return LinkedOrdersForViewing
     */
    private function getLinkedOrders(Order $order): LinkedOrdersForViewing
    {
        $brothersData = $order->getBrother();
        $brothers = [];
        /** @var Order $brotherItem */
        foreach ($brothersData as $brotherItem) {
            $isTaxExcluded = !$this->isTaxIncludedInOrder($brotherItem);

            $currency = new Currency($brotherItem->id_currency);

            if ($isTaxExcluded) {
                $totalAmount = $this->locale->formatPrice($brotherItem->total_paid_tax_excl, $currency->iso_code);
            } else {
                $totalAmount = $this->locale->formatPrice($brotherItem->total_paid_tax_incl, $currency->iso_code);
            }

            $orderState = new OrderState($brotherItem->current_state);

            $brothers[] = new LinkedOrderForViewing(
                $brotherItem->id,
                $orderState->name[$this->context->language->getId()],
                $totalAmount
            );
        }

        return new LinkedOrdersForViewing($brothers);
    }

    /**
     * @param OrderId $orderId
     * @param string $productsOrder
     *
     * @return OrderProductsForViewing
     *
     * @throws OrderException
     * @throws InvalidSortingException
     */
    private function getOrderProducts(OrderId $orderId, string $productsOrder): OrderProductsForViewing
    {
        return $this->getOrderProductsForViewingHandler->handle(
            GetOrderProductsForViewing::all($orderId->getValue(), $productsOrder)
        );
    }

    /**
     * If there is no valid customer attached to the order, the customer must have been deleted
     * from the database. We then create a fake customer object, using the invoice address data
     * and cart language.
     *
     * @param Order $order Order object
     * @param OrderInvoiceAddressForViewing $invoiceAddress Invoice address information
     *
     * @return Customer The created customer
     */
    private function buildFakeCustomerObject(Order $order, OrderInvoiceAddressForViewing $invoiceAddress): Customer
    {
        $cart = new Cart($order->id_cart);

        $customer = new Customer();
        $customer->firstname = $invoiceAddress->getFirstName();
        $customer->lastname = $invoiceAddress->getLastName();
        $customer->email = '';
        $customer->id_lang = $cart->getAssociatedLanguage()->getId();
        $customer->is_guest = true;

        return $customer;
    }
}
