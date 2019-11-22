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

namespace PrestaShop\PrestaShop\Adapter\Order\QueryHandler;

use Address;
use Carrier;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use DateTimeImmutable;
use Db;
use Gender;
use Group;
use Image;
use ImageManager;
use Module;
use Order;
use OrderInvoice;
use OrderPayment;
use OrderReturn;
use OrderSlip;
use Pack;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderForViewingHandlerInterface;
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
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderReturnForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderReturnsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderStatusForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use Shop;
use State;
use StockAvailable;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;
use Warehouse;
use WarehouseProductLocation;

/**
 * Handle getting order for viewing
 *
 * @internal
 */
final class GetOrderForViewingHandler implements GetOrderForViewingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

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
     * @var Context
     */
    private $context;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     * @param TranslatorInterface $translator
     * @param int $contextLanguageId
     * @param Locale $locale
     * @param Context $context
     * @param CustomerDataProvider $customerDataProvider
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        TranslatorInterface $translator,
        int $contextLanguageId,
        Locale $locale,
        Context $context,
        CustomerDataProvider $customerDataProvider
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->translator = $translator;
        $this->contextLanguageId = $contextLanguageId;
        $this->locale = $locale;
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->translator = $translator;
        $this->context = $context;
        $this->customerDataProvider = $customerDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderForViewing $query): OrderForViewing
    {
        $order = $this->getOrder($query->getOrderId());
        $taxCalculationMethod = $this->getOrderTaxCalculationMethod($order);

        $isTaxIncluded = ($taxCalculationMethod == PS_TAX_INC);

        $taxMethod = $isTaxIncluded ?
            $this->translator->trans('Tax included', [], 'Admin.Global') :
            $this->translator->trans('Tax excluded', [], 'Admin.Global');

        $invoiceManagementIsEnabled = (bool) Configuration::get('PS_INVOICE', null, null, $order->id_shop);

        return new OrderForViewing(
            (int) $order->id,
            (int) $order->id_currency,
            (int) $order->id_carrier,
            (int) $order->id_shop,
            $order->reference,
            (bool) $order->isVirtual(),
            $taxMethod,
            $isTaxIncluded,
            (bool) $order->valid,
            $order->hasInvoice(),
            $order->hasBeenDelivered(),
            $invoiceManagementIsEnabled,
            new DateTimeImmutable($order->date_add),
            $this->getOrderCustomer($order),
            $this->getOrderShippingAddress($order),
            $this->getOrderInvoiceAddress($order),
            $this->getOrderProducts($order),
            $this->getOrderHistory($order),
            $this->getOrderDocuments($order),
            $this->getOrderShipping($order),
            $this->getOrderReturns($order),
            $this->getOrderPayments($order),
            $this->getOrderMessages($order),
            $this->getOrderPrices($order),
            $this->getOrderDiscounts($order)
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
     * @return OrderCustomerForViewing
     */
    private function getOrderCustomer(Order $order): OrderCustomerForViewing
    {
        $currency = new Currency($order->id_currency);
        $customer = new Customer($order->id_customer);
        $gender = new Gender($customer->id_gender);
        $genderName = '';

        if (Validate::isLoadedObject($gender)) {
            $genderName = $gender->name[$order->id_lang];
        }

        $customerStats = $customer->getStats();
        $totalSpentSinceRegistration = Tools::convertPrice($customerStats['total_orders'], $order->id_currency);

        return new OrderCustomerForViewing(
            $customer->id,
            $customer->firstname,
            $customer->lastname,
            $genderName,
            $customer->email,
            new DateTimeImmutable($customer->date_add),
            $totalSpentSinceRegistration !== null ? $this->locale->formatPrice($totalSpentSinceRegistration, $currency->iso_code) : '',
            $customerStats['nb_orders'],
            $customer->note
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

        return new OrderShippingAddressForViewing(
            $address->id,
            $address->firstname,
            $address->lastname,
            $address->company,
            $address->address1,
            $address->address2,
            $stateName,
            $address->city,
            $country->name[$order->id_lang],
            $address->postcode,
            $address->phone,
            $address->phone_mobile
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

        return new OrderInvoiceAddressForViewing(
            $address->id,
            $address->firstname,
            $address->lastname,
            $address->company,
            $address->address1,
            $address->address2,
            $stateName,
            $address->city,
            $country->name[$order->id_lang],
            $address->postcode,
            $address->phone,
            $address->phone_mobile
        );
    }

    /**
     * @param Order $order
     *
     * @return OrderProductsForViewing
     */
    private function getOrderProducts(Order $order): OrderProductsForViewing
    {
        $taxCalculationMethod = $this->getOrderTaxCalculationMethod($order);

        $products = $order->getProducts();
        $currency = new Currency((int) $order->id_currency);

        $display_out_of_stock_warning = false;
        $current_order_state = $order->getCurrentOrderState();
        if (Configuration::get('PS_STOCK_MANAGEMENT') && (!Validate::isLoadedObject($current_order_state) || ($current_order_state->delivery != 1 && $current_order_state->shipped != 1))) {
            $display_out_of_stock_warning = true;
        }

        foreach ($products as &$product) {
            if ($product['image'] instanceof Image) {
                $name = 'product_mini_' . (int) $product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $product['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                } else {
                    $product['image_size'] = false;
                }
            }

            // Get total customized quantity for current product
            $customized_product_quantity = 0;

            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customizationId => $customization) {
                        $customized_product_quantity += (int) $customization['quantity'];
                    }
                }
            }

            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
            $resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
            $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
            $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['displayed_max_refundable'] = $order->getTaxCalculationMethod() ? $product['amount_refundable'] : $product['amount_refundable_tax_incl'];
            $resumeAmount = $order->getTaxCalculationMethod() ? 'amount_tax_excl' : 'amount_tax_incl';
            $product['amount_refund'] = $resume[$resumeAmount] ?? 0;
            $product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
            $product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);

            // if the current stock requires a warning
            if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                // @todo
                //$this->displayWarning($this->trans('This product is out of stock: ', array(), 'Admin.Orderscustomers.Notification') . ' ' . $product['product_name']);
            }
            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int) $product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
                if (!empty($warehouse_location)) {
                    $product['warehouse_location'] = $warehouse_location;
                } else {
                    $product['warehouse_location'] = false;
                }
            } else {
                $product['warehouse_name'] = '--';
                $product['warehouse_location'] = false;
            }

            if (!empty($product['location'])) {
                $stockLocationIsAvailable = true;
            }

            $pack_items = $product['cache_is_pack'] ? Pack::getItemTable($product['id_product'], $this->contextLanguageId, true) : array();
            foreach ($pack_items as &$pack_item) {
                $pack_item['current_stock'] = StockAvailable::getQuantityAvailableByProduct($pack_item['id_product'], $pack_item['id_product_attribute'], $pack_item['id_shop']);
                // if the current stock requires a warning
                if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                    // @todo
                    // $this->displayWarning($this->trans('This product, included in package (' . $product['product_name'] . ') is out of stock: ', array(), 'Admin.Orderscustomers.Notification') . ' ' . $pack_item['product_name']);
                }
                $this->setProductImageInformation($pack_item);
                if ($pack_item['image'] instanceof Image) {
                    $name = 'product_mini_' . (int) $pack_item['id_product'] . (isset($pack_item['id_product_attribute']) ? '_' . (int) $pack_item['id_product_attribute'] : '') . '.jpg';
                    // generate image cache, only for back office
                    $pack_item['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $pack_item['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $pack_item['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $pack_item['image_size'] = false;
                    }
                }
            }

            unset($pack_item);

            $product['pack_items'] = $pack_items;
        }

        unset($product);

        ksort($products);

        $productsForViewing = [];

        $isOrderTaxExcluded = ($taxCalculationMethod == PS_TAX_EXC);
        $computingPrecision = new ComputingPrecision();

        foreach ($products as $product) {
            $unitPrice = $isOrderTaxExcluded ?
                $product['unit_price_tax_excl'] :
                $product['unit_price_tax_incl']
            ;

            $totalPrice = $unitPrice *
                (!empty($product['customizedDatas']) ? $product['customizationQuantityTotal'] : $product['product_quantity']);

            $unitPriceFormatted = $this->locale->formatPrice($unitPrice, $currency->iso_code);
            $totalPriceFormatted = $this->locale->formatPrice($totalPrice, $currency->iso_code);

            $imagePath = isset($product['image_tag']) ?
                $this->imageTagSourceParser->parse($product['image_tag']) :
                null;
            $product['product_quantity_refunded'] = $product['product_quantity_refunded'] ?: false;

            $productsForViewing[] = new OrderProductForViewing(
                $product['id_order_detail'],
                $product['product_id'],
                $product['product_name'],
                $product['reference'],
                $product['supplier_reference'],
                $product['product_quantity'],
                $unitPriceFormatted,
                $totalPriceFormatted,
                $product['current_stock'],
                $imagePath,
                Tools::ps_round(
                    $product['unit_price_tax_excl'],
                    $computingPrecision->getPrecision($currency->precision)
                ),
                Tools::ps_round(
                    $product['unit_price_tax_incl'],
                    $computingPrecision->getPrecision($currency->precision)
                ),
                $product['tax_rate'],
                $this->locale->formatPrice($product['amount_refund'], $currency->iso_code),
                $product['product_quantity_refunded'],
                $this->locale->formatPrice($product['displayed_max_refundable'], $currency->iso_code)
            );
        }

        return new OrderProductsForViewing($productsForViewing);
    }

    /**
     * @param $pack_item
     */
    private function setProductImageInformation(&$pack_item): void
    {
        if (isset($pack_item['id_product_attribute']) && $pack_item['id_product_attribute']) {
            $id_image = Db::getInstance()->getValue('
                SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai' .
                Shop::addSqlAssociation('image', 'pai', true) . '
                WHERE id_product_attribute = ' . (int) $pack_item['id_product_attribute']);
        }

        if (!isset($id_image) || !$id_image) {
            $id_image = Db::getInstance()->getValue(
                '
                SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'image` i' .
                Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1') . '
                WHERE i.id_product = ' . (int) $pack_item['id_product']
            );
        }

        $pack_item['image'] = null;
        $pack_item['image_size'] = null;

        if ($id_image) {
            $pack_item['image'] = new Image($id_image);
        }
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
            $amountMismatch = null;
            $availableAction = null;
            $isAddPaymentAllowed = false;

            if (get_class($document) === 'OrderInvoice') {
                $type = isset($document->is_delivery) ? 'delivery_slip' : 'invoice';
            } elseif (get_class($document) === 'OrderSlip') {
                $type = 'credit_slip';
            }

            if ('invoice' === $type) {
                $number = $document->getInvoiceNumberFormatted(
                    $this->contextLanguageId,
                    $order->id_shop
                );

                if ($document->getRestPaid()) {
                    $isAddPaymentAllowed = true;
                }
            } elseif ('delivery_slip' === $type) {
                $number = sprintf(
                    '%s%06d',
                    Configuration::get('PS_DELIVERY_PREFIX', $this->contextLanguageId, null, $order->id_shop),
                    $document->delivery_number
                );
            } elseif ('credit_slip' === $type) {
                $number = sprintf(
                    '%s%06d',
                    Configuration::get('PS_CREDIT_SLIP_PREFIX', $this->contextLanguageId),
                    $document->id
                );
            }

            if ($document instanceof OrderInvoice && !isset($document->is_delivery)) {
                $amount = $this->locale->formatPrice($document->total_paid_tax_incl, $currency->iso_code);

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
            } elseif ($document instanceof OrderSlip) {
                $amount = $this->locale->formatPrice(
                    $document->total_products_tax_incl + $document->total_shipping_tax_incl,
                    $currency->iso_code
                );
            }
            $documentsForViewing[] = new OrderDocumentForViewing(
                $document->id,
                $type,
                new DateTimeImmutable($document->date_add),
                $number,
                $document->total_paid_tax_incl ?? null,
                $amount,
                $amountMismatch,
                $document instanceof OrderInvoice ? $document->note : null,
                $isAddPaymentAllowed
            );
        }

        $canGenerateInvoice = Configuration::get('PS_INVOICE') &&
            count($order->getInvoicesCollection()) &&
            $order->invoice_number;

        $canGenerateDeliverySlip = (bool) $order->delivery_number;

        return new OrderDocumentsForViewing(
            $canGenerateInvoice,
            $canGenerateDeliverySlip,
            $documentsForViewing
        );
    }

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

                if ($item['url'] && $item['tracking_number']) {
                    $trackingUrl = str_replace('@', $item['tracking_number'], $item['url']);
                }

                $weight = sprintf('%.3f %s', $item['weight'], Configuration::get('PS_WEIGHT_UNIT'));

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

    private function getOrderPrices(Order $order): OrderPricesForViewing
    {
        $currency = new Currency($order->id_currency);
        $customer = $order->getCustomer();

        $isTaxExcluded = ($this->getOrderTaxCalculationMethod($order) == PS_TAX_EXC);

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

        $taxesAmount = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
        $totalAmount = (float) $order->total_paid_tax_incl;

        return new OrderPricesForViewing(
            $productsPrice,
            $discountsAmount,
            $wrappingPrice,
            $shippingPrice,
            $shippingRefundable,
            $taxesAmount,
            $totalAmount,
            Tools::displayPrice($productsPrice, $currency),
            Tools::displayPrice($discountsAmount, $currency),
            Tools::displayPrice($wrappingPrice, $currency),
            Tools::displayPrice($shippingPrice, $currency),
            Tools::displayPrice($shippingRefundable, $currency),
            Tools::displayPrice($taxesAmount, $currency),
            Tools::displayPrice($totalAmount, $currency)
        );
    }

    private function getOrderDiscounts(Order $order): OrderDiscountsForViewing
    {
        $currency = new Currency($order->id_currency);
        $discounts = $order->getCartRules();
        $discountsForViewing = [];

        foreach ($discounts as $discount) {
            $discountsForViewing[] = new OrderDiscountForViewing(
                (int) $discount['id_order_cart_rule'],
                $discount['name'],
                (float) $discount['value'],
                Tools::displayPrice($discount['value'], $currency)
            );
        }

        return new OrderDiscountsForViewing($discountsForViewing);
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
