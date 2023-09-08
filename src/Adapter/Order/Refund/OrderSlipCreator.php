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

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use Address;
use Carrier;
use Currency;
use Customer;
use Db;
use Hook;
use Mail;
use Order;
use OrderDetail;
use OrderSlip;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use PrestaShopDatabaseException;
use PrestaShopException;
use StockAvailable;
use Symfony\Contracts\Translation\TranslatorInterface;
use TaxCalculator;
use TaxManagerFactory;
use Tools;

/**
 * Class OrderSlipCreator is responsible of creating an OrderSlip for a refund
 */
class OrderSlipCreator
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * @param Order $order
     * @param OrderRefundSummary $orderRefundSummary
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function create(
        Order $order,
        OrderRefundSummary $orderRefundSummary
    ) {
        if ($orderRefundSummary->getRefundedAmount() > 0) {
            $orderSlipCreated = $this->createOrderSlip(
                $order,
                $orderRefundSummary->getProductRefunds(),
                $orderRefundSummary->getRefundedShipping(),
                $orderRefundSummary->getVoucherAmount(),
                $orderRefundSummary->isVoucherChosen(),
                !$orderRefundSummary->isTaxIncluded(),
                $orderRefundSummary->getPrecision()
            );

            if (!$orderSlipCreated) {
                throw new OrderException('You cannot generate a partial credit slip.');
            }

            $fullQuantityList = array_map(function ($orderDetail) { return $orderDetail['quantity']; }, $orderRefundSummary->getProductRefunds());

            // Hook called only for the shop concerned
            Hook::exec('actionOrderSlipAdd', [
                'order' => $order,
                'productList' => $orderRefundSummary->getProductRefunds(),
                'qtyList' => $fullQuantityList,
            ], null, false, true, false, $order->id_shop);

            $customer = new Customer((int) $order->id_customer);

            // @todo: use private method to send mail
            $params = [
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{id_order}' => $order->id,
                '{order_name}' => $order->getUniqReference(),
            ];

            $orderLanguage = $order->getAssociatedLanguage();

            // @todo: use a dedicated Mail class (see #13945)
            // @todo: remove this @and have a proper error handling
            @Mail::Send(
                (int) $orderLanguage->getId(),
                'credit_slip',
                $this->translator->trans(
                    'New credit slip regarding your order',
                    [],
                    'Emails.Subject',
                    $orderLanguage->locale
                ),
                $params,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                true,
                (int) $order->id_shop
            );

            /** @var OrderDetail $orderDetail */
            foreach ($orderRefundSummary->getOrderDetails() as $orderDetail) {
                if ($this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    StockAvailable::synchronize($orderDetail->product_id);
                }
            }
        } else {
            throw new InvalidCancelProductException(InvalidCancelProductException::INVALID_AMOUNT);
        }
    }

    /**
     * This is a copy of OrderSlip::create except the OrderDetail modification has been removed
     * since it's now managed in the handler, this allows to update order details even without
     * generating a credit slip
     *
     * @todo this copy uses array data but could probably be refactored to use OrderDetailRefund objects
     *
     * @param Order $order
     * @param array $product_list
     * @param float $shipping_cost
     * @param float $amount
     * @param bool $amount_choosen
     * @param bool $add_tax
     * @param int $precision
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function createOrderSlip(
        Order $order,
        array $product_list,
        float $shipping_cost = 0,
        float $amount = 0,
        bool $amount_choosen = false,
        bool $add_tax = true,
        int $precision = 6
    ) {
        $currency = new Currency((int) $order->id_currency);
        $orderSlip = new OrderSlip();
        $orderSlip->id_customer = (int) $order->id_customer;
        $orderSlip->id_order = (int) $order->id;
        $orderSlip->conversion_rate = $currency->conversion_rate;

        $orderSlip->total_shipping_tax_excl = 0;
        $orderSlip->total_shipping_tax_incl = 0;
        $orderSlip->partial = 0;

        if ($shipping_cost > 0) {
            $orderSlip->shipping_cost = true;
            $carrier = new Carrier((int) $order->id_carrier);
            // @todo: define if we use invoice or delivery address, or we use configuration PS_TAX_ADDRESS_TYPE
            $address = Address::initialize($order->id_address_delivery, false);
            $tax_calculator = $carrier->getTaxCalculator($address);

            if ($add_tax) {
                $orderSlip->total_shipping_tax_excl = $shipping_cost;
                if ($tax_calculator instanceof TaxCalculator) {
                    $orderSlip->total_shipping_tax_incl = Tools::ps_round($tax_calculator->addTaxes($orderSlip->total_shipping_tax_excl), $precision);
                } else {
                    $orderSlip->total_shipping_tax_incl = $orderSlip->total_shipping_tax_excl;
                }
            } else {
                $orderSlip->total_shipping_tax_incl = $shipping_cost;
                if ($tax_calculator instanceof TaxCalculator) {
                    $orderSlip->total_shipping_tax_excl = Tools::ps_round($tax_calculator->removeTaxes($orderSlip->total_shipping_tax_incl), $precision);
                } else {
                    $orderSlip->total_shipping_tax_excl = $orderSlip->total_shipping_tax_incl;
                }
            }
        } else {
            $orderSlip->shipping_cost = false;
        }

        $orderSlip->amount = 0;
        $orderSlip->total_products_tax_excl = 0;
        $orderSlip->total_products_tax_incl = 0;
        $total_products = [];
        foreach ($product_list as &$product) {
            $order_detail = new OrderDetail((int) $product['id_order_detail']);
            $price = (float) $product['unit_price'];
            $quantity = (int) $product['quantity'];

            // @todo: define if we use invoice or delivery address, or we use configuration PS_TAX_ADDRESS_TYPE
            $address = Address::initialize($order->id_address_invoice, false);
            $id_address = (int) $address->id;
            $id_tax_rules_group = (int) $order_detail->id_tax_rules_group;
            $tax_calculator = $order_detail->getTaxCalculator();

            if ($add_tax) {
                $orderSlip->total_products_tax_excl += $price * $quantity;
            } else {
                $orderSlip->total_products_tax_incl += $price * $quantity;
            }

            if (in_array($this->configuration->get('PS_ROUND_TYPE'), [Order::ROUND_ITEM, Order::ROUND_LINE])) {
                if (!isset($total_products[$id_tax_rules_group])) {
                    $total_products[$id_tax_rules_group] = 0;
                }
            } else {
                if (!isset($total_products[$id_tax_rules_group . '_' . $id_address])) {
                    $total_products[$id_tax_rules_group . '_' . $id_address] = 0;
                }
            }

            if ($add_tax) {
                $product_tax_incl_line = Tools::ps_round($tax_calculator->addTaxes($price) * $quantity, $precision);
            } else {
                $product_tax_incl_line = Tools::ps_round($tax_calculator->removeTaxes($price) * $quantity, $precision);
            }
            switch ($this->configuration->get('PS_ROUND_TYPE')) {
                case Order::ROUND_ITEM:
                    if ($add_tax) {
                        $product_tax_incl = Tools::ps_round($tax_calculator->addTaxes($price), $precision) * $quantity;
                    } else {
                        $product_tax_incl = Tools::ps_round($tax_calculator->removeTaxes($price), $precision) * $quantity;
                    }
                    $total_products[$id_tax_rules_group] += $product_tax_incl;
                    break;
                case Order::ROUND_LINE:
                    $product_tax_incl = $product_tax_incl_line;
                    $total_products[$id_tax_rules_group] += $product_tax_incl;
                    break;
                case Order::ROUND_TOTAL:
                    $product_tax_incl = $product_tax_incl_line;
                    $total_products[$id_tax_rules_group . '_' . $id_address] += $price * $quantity;
                    break;
                default:
                    $product_tax_incl = 0;
            }

            if ($add_tax) {
                $product['unit_price_tax_excl'] = $price;
                $product['unit_price_tax_incl'] = Tools::ps_round($tax_calculator->addTaxes($price), $precision);
                $product['total_price_tax_excl'] = Tools::ps_round($price * $quantity, $precision);
                $product['total_price_tax_incl'] = Tools::ps_round($product_tax_incl, $precision);
            } else {
                $product['unit_price_tax_incl'] = $price;
                $product['unit_price_tax_excl'] = Tools::ps_round($tax_calculator->removeTaxes($price), $precision);
                $product['total_price_tax_incl'] = Tools::ps_round($price * $quantity, $precision);
                $product['total_price_tax_excl'] = Tools::ps_round($product_tax_incl, $precision);
            }
        }

        unset($product);

        foreach ($total_products as $key => $price) {
            if ($this->configuration->get('PS_ROUND_TYPE') == Order::ROUND_TOTAL) {
                $tmp = explode('_', $key);
                $address = Address::initialize((int) $tmp[1], true);
                $tax_calculator = TaxManagerFactory::getManager($address, (int) $tmp[0])->getTaxCalculator();

                if ($add_tax) {
                    $orderSlip->total_products_tax_incl += Tools::ps_round($tax_calculator->addTaxes($price), $precision);
                } else {
                    $orderSlip->total_products_tax_excl += Tools::ps_round($tax_calculator->removeTaxes($price), $precision);
                }
            } else {
                if ($add_tax) {
                    $orderSlip->total_products_tax_incl += $price;
                } else {
                    $orderSlip->total_products_tax_excl += $price;
                }
            }
        }

        if ($add_tax) {
            $orderSlip->total_products_tax_incl -= $amount && !$amount_choosen ? $amount : 0;
            $orderSlip->amount = $amount_choosen ? $amount : $orderSlip->total_products_tax_excl;
        } else {
            $orderSlip->total_products_tax_excl -= $amount && !$amount_choosen ? $amount : 0;
            $orderSlip->amount = $amount_choosen ? $amount : $orderSlip->total_products_tax_incl;
        }
        $orderSlip->shipping_cost_amount = $orderSlip->total_shipping_tax_incl;

        if ((float) $amount && !$amount_choosen) {
            $orderSlip->order_slip_type = VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND;
        }
        if (((float) $amount && $amount_choosen) || $orderSlip->shipping_cost_amount > 0) {
            $orderSlip->order_slip_type = VoucherRefundType::SPECIFIC_AMOUNT_REFUND;
        }

        if (!$orderSlip->add()) {
            return false;
        }

        $res = true;

        foreach ($product_list as $product) {
            $res &= $this->addProductOrderSlip((int) $orderSlip->id, $product);
        }

        return (bool) $res;
    }

    /**
     * @param array $product
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    private function addProductOrderSlip(int $orderSlipId, array $product): bool
    {
        return (bool) Db::getInstance()->insert('order_slip_detail', [
            'id_order_slip' => $orderSlipId,
            'id_order_detail' => (int) $product['id_order_detail'],
            'product_quantity' => $product['quantity'],
            'unit_price_tax_excl' => $product['unit_price_tax_excl'],
            'unit_price_tax_incl' => $product['unit_price_tax_incl'],
            'total_price_tax_excl' => $product['total_price_tax_excl'],
            'total_price_tax_incl' => $product['total_price_tax_incl'],
            'amount_tax_excl' => $product['total_price_tax_excl'],
            'amount_tax_incl' => $product['total_price_tax_incl'],
        ]);
    }
}
