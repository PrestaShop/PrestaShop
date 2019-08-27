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

use Address;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use DateTimeImmutable;
use Db;
use Gender;
use Image;
use ImageManager;
use Order;
use OrderReturn;
use OrderSlip;
use Pack;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderInvoiceAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;
use Shop;
use State;
use StockAvailable;
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
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     */
    public function __construct(ImageTagSourceParserInterface $imageTagSourceParser)
    {
        $this->imageTagSourceParser = $imageTagSourceParser;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderForViewing $query): OrderForViewing
    {
        $order = $this->getOrder($query->getOrderId());

        $taxMethod = $order->getTaxCalculationMethod() == PS_TAX_EXC ?
            Context::getContext()->getTranslator()->trans('Tax excluded', [], 'Admin.Global') :
            Context::getContext()->getTranslator()->trans('Tax included', [], 'Admin.Global');

        return new OrderForViewing(
            $order->reference,
            $taxMethod,
            $this->getOrderCustomer($order),
            $this->getOrderShippingAddress($order),
            $this->getOrderInvoiceAddress($order),
            $this->getOrderProducts($order)
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
            throw new OrderNotFoundException(sprintf('Order with id "%s" was not found.',$orderId->getValue()));
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
        $customer = new Customer($order->id_customer);
        $gender = new Gender($customer->id_gender);
        $genderName = '';

        if (Validate::isLoadedObject($gender)) {
            $genderName = $gender->name[$order->id_lang];
        }

        $customerStats = $customer->getStats();

        return new OrderCustomerForViewing(
            $customer->id,
            $customer->firstname,
            $customer->lastname,
            $genderName,
            $customer->email,
            new DateTimeImmutable($customer->date_add),
            Tools::displayPrice(Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $order->id_currency), PS_ROUND_HALF_UP), (int) $order->id_currency),
            $customerStats['nb_orders']
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

    private function getOrderProducts(Order $order): OrderProductsForViewing
    {
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
            $product['amount_refund'] = $order->getTaxCalculationMethod() ? Tools::displayPrice($resume['amount_tax_excl'], $currency) : Tools::displayPrice($resume['amount_tax_incl'], $currency);
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

            $pack_items = $product['cache_is_pack'] ? Pack::getItemTable($product['id_product'], Context::getContext()->language->id, true) : array();
            foreach ($pack_items as &$pack_item) {
                $pack_item['current_stock'] = StockAvailable::getQuantityAvailableByProduct($pack_item['id_product'], $pack_item['id_product_attribute'], $pack_item['id_shop']);
                // if the current stock requires a warning
                if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                    // @todo
                    // $this->displayWarning($this->trans('This product, included in package (' . $product['product_name'] . ') is out of stock: ', array(), 'Admin.Orderscustomers.Notification') . ' ' . $pack_item['product_name']);
                }
                $this->setProductImageInformations($pack_item);
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

        dump($products);

        $productsForViewing = [];

        $isOrderTaxExcluded = $order->getTaxCalculationMethod() == PS_TAX_EXC;

        foreach ($products as $product) {
            $unitPrice = $isOrderTaxExcluded ?
                $product['unit_price_tax_excl'] :
                $product['unit_price_tax_incl']
            ;
            $totalPrice = Tools::ps_round($unitPrice, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal']);

            $unitPriceFormatted = Tools::displayPrice($unitPrice, $currency);
            $totalPriceFormatted = Tools::displayPrice($totalPrice, $currency);

            $productsForViewing[] = new OrderProductForViewing(
                $product['product_id'],
                $product['product_name'],
                $product['reference'],
                $product['supplier_reference'],
                $product['product_quantity'],
                $unitPriceFormatted,
                $totalPriceFormatted,
                $product['current_stock'],
                $this->imageTagSourceParser->parse($product['image_tag'])
            );
        }

        return new OrderProductsForViewing($productsForViewing);
    }

    private function setProductImageInformations(&$pack_item): void
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
}
