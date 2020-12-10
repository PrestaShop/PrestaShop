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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Address;
use Country;
use Currency;
use Db;
use Order;
use OrderDetail;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use Shop;
use TaxCalculator;
use TaxManagerFactory;
use Tools;

class OrderDetailTaxUpdater
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var ShopConfigurationInterface
     */
    private $shopConfiguration;

    /**
     * @param ContextStateManager $contextStateManager
     * @param ShopConfigurationInterface $shopConfiguration
     */
    public function __construct(
        ContextStateManager $contextStateManager,
        ShopConfigurationInterface $shopConfiguration
    ) {
        $this->contextStateManager = $contextStateManager;
        $this->shopConfiguration = $shopConfiguration;
    }

    /**
     * @param Order $order
     */
    public function updateOrderDetailsTaxes(Order $order): void
    {
        $shopConstraint = new ShopConstraint(
            (int) $order->id_shop,
            (int) $order->id_shop_group
        );
        $roundType = (int) $this->shopConfiguration->get('PS_ROUND_TYPE', null, $shopConstraint);
        $taxAddressType = $this->shopConfiguration->get('PS_TAX_ADDRESS_TYPE', null, $shopConstraint);
        $taxAddress = new Address($order->{$taxAddressType});
        $country = new Country($taxAddress->id_country);
        $currency = new Currency($order->id_currency);
        $shop = new Shop($order->id_shop);
        $this->contextStateManager
            ->setCurrency($currency)
            ->setCountry($country)
            ->setShop($shop)
        ;
        $computingPrecision = (new ComputingPrecision())->getPrecision((int) $currency->precision);

        try {
            $orderDetailsData = $order->getProducts();
            foreach ($orderDetailsData as $orderDetailData) {
                $orderDetail = new OrderDetail($orderDetailData['id_order_detail']);

                // Clean existing order_detail_tax
                Db::getInstance()->delete('order_detail_tax', 'id_order_detail = ' . (int) $orderDetail->id);

                $taxCalculator = $this->getTaxCalculatorByAddress($taxAddress, $orderDetail);
                $taxesAmount = $taxCalculator->getTaxesAmount($orderDetail->unit_price_tax_excl);
                $unitAmount = $totalAmount = 0;
                if (!empty($taxesAmount)) {
                    $orderDetailTaxes = [];
                    foreach ($taxesAmount as $taxId => $amount) {
                        switch ($roundType) {
                            case Order::ROUND_ITEM:
                                $unitAmount = (float) Tools::ps_round($amount, $computingPrecision);
                                $totalAmount = $unitAmount * $orderDetail->product_quantity;

                                break;
                            case Order::ROUND_LINE:
                                $unitAmount = $amount;
                                $totalAmount = Tools::ps_round($unitAmount * $orderDetail->product_quantity, $computingPrecision);

                                break;
                            case Order::ROUND_TOTAL:
                                $unitAmount = $amount;
                                $totalAmount = $unitAmount * $orderDetail->product_quantity;

                                break;
                        }
                        $orderDetailTaxes[] = [
                            'id_order_detail' => $orderDetail->id,
                            'id_tax' => $taxId,
                            'unit_amount' => (float) $unitAmount,
                            'total_amount' => (float) $totalAmount,
                        ];
                        $orderDetail->unit_price_tax_incl = (float) $orderDetail->unit_price_tax_excl + $unitAmount;
                        $orderDetail->total_price_tax_incl = (float) $orderDetail->total_price_tax_incl + $totalAmount;
                    }

                    Db::getInstance()->insert('order_detail_tax', $orderDetailTaxes, false);
                }

                // Update OrderDetail values
                $orderDetail->unit_price_tax_incl = (float) $orderDetail->unit_price_tax_excl + $unitAmount;
                $orderDetail->total_price_tax_incl = (float) $orderDetail->total_price_tax_incl + $totalAmount;
                $orderDetail->save();
            }
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * Get a TaxCalculator adapted for the OrderDetail's product and the specified address
     *
     * @param Address $address
     * @param OrderDetail $orderDetail
     *
     * @return TaxCalculator
     */
    private function getTaxCalculatorByAddress(Address $address, OrderDetail $orderDetail): TaxCalculator
    {
        $tax_manager = TaxManagerFactory::getManager($address, $orderDetail->getTaxRulesGroupId());

        return $tax_manager->getTaxCalculator();
    }
}
