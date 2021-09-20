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
use Cart;
use Country;
use Currency;
use Customer;
use Db;
use Order;
use OrderDetail;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use Product;
use Shop;
use TaxCalculator;
use TaxManagerFactory;
use Tools;

class OrderDetailUpdater
{
    private const COMPARISON_PRECISION = 6;

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
     * @param OrderDetail $orderDetail
     * @param Order $order
     * @param DecimalNumber $priceTaxExcluded
     * @param DecimalNumber $priceTaxIncluded
     *
     * @throws OrderException
     */
    public function updateOrderDetail(
        OrderDetail $orderDetail,
        Order $order,
        DecimalNumber $priceTaxExcluded,
        DecimalNumber $priceTaxIncluded
    ): void {
        list($roundType, $computingPrecision, $taxAddress) = $this->prepareOrderContext($order);

        try {
            $ecotax = new DecimalNumber((string) $orderDetail->ecotax);

            $ecotaxTaxCalculator = $this->getTaxCalculatorForEcotax($taxAddress);
            $ecotaxTaxFactor = new DecimalNumber((string) (1 + ($ecotaxTaxCalculator->getTotalRate() / 100)));
            $ecotaxTaxIncluded = $ecotax->times($ecotaxTaxFactor);

            // Prices coming from the backoffice : they are displayed with ecotax
            // So we need to remove ecotax before having precise price
            $priceTaxExcluded = $priceTaxExcluded->minus($ecotax);
            $priceTaxIncluded = $priceTaxIncluded->minus($ecotaxTaxIncluded);

            $precisePriceTaxExcluded = $this->getPrecisePriceTaxExcluded($priceTaxIncluded, $priceTaxExcluded, $order, $orderDetail, $taxAddress);
            $precisePriceTaxIncluded = $this->getPrecisePriceTaxIncluded($priceTaxIncluded, $priceTaxExcluded, $order, $orderDetail, $taxAddress);

            $precisePriceTaxExcluded = $precisePriceTaxExcluded->plus($ecotax);
            $precisePriceTaxIncluded = $precisePriceTaxIncluded->plus($ecotaxTaxIncluded);

            $this->applyOrderDetailPriceUpdate(
                $orderDetail,
                $precisePriceTaxExcluded,
                $precisePriceTaxIncluded,
                $roundType,
                $computingPrecision
            );
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * @param Order $order
     * @param int $productId
     * @param int $combinationId
     * @param DecimalNumber $priceTaxExcluded
     * @param DecimalNumber $priceTaxIncluded
     *
     * @throws OrderException
     */
    public function updateOrderDetailsForProduct(
        Order $order,
        int $productId,
        int $combinationId,
        DecimalNumber $priceTaxExcluded,
        DecimalNumber $priceTaxIncluded
    ): void {
        list($roundType, $computingPrecision, $taxAddress) = $this->prepareOrderContext($order);

        try {
            $this->applyUpdatesForProduct(
                $order,
                $productId,
                $combinationId,
                $priceTaxExcluded,
                $priceTaxIncluded,
                $roundType,
                $computingPrecision,
                $taxAddress
            );
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * @param Order $order
     */
    public function updateOrderDetailsTaxes(Order $order): void
    {
        list($roundType, $computingPrecision, $taxAddress) = $this->prepareOrderContext($order);

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
                if (!$orderDetail->update()) {
                    throw new OrderException('An error occurred while editing the product line.');
                }
            }
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function prepareOrderContext(Order $order): array
    {
        $shopConstraint = ShopConstraint::shop((int) $order->id_shop);
        $roundType = (int) $this->shopConfiguration->get('PS_ROUND_TYPE', null, $shopConstraint);
        $taxAddressType = $this->shopConfiguration->get('PS_TAX_ADDRESS_TYPE', null, $shopConstraint);
        $taxAddress = new Address($order->{$taxAddressType});
        $country = new Country($taxAddress->id_country);
        $currency = new Currency($order->id_currency);
        $shop = new Shop($order->id_shop);
        $computingPrecision = (new ComputingPrecision())->getPrecision((int) $currency->precision);

        $this->contextStateManager
            ->saveCurrentContext()
            ->setCart(new Cart($order->id_cart))
            ->setCustomer(new Customer($order->id_customer))
            ->setLanguage($order->getAssociatedLanguage())
            ->setCurrency($currency)
            ->setCountry($country)
            ->setShop($shop)
        ;

        return [$roundType, $computingPrecision, $taxAddress];
    }

    /**
     * @param OrderDetail $orderDetail
     * @param DecimalNumber $priceTaxExcluded
     * @param DecimalNumber $priceTaxIncluded
     * @param int $roundType
     * @param int $computingPrecision
     *
     * @throws OrderException
     */
    private function applyOrderDetailPriceUpdate(
        OrderDetail $orderDetail,
        DecimalNumber $priceTaxExcluded,
        DecimalNumber $priceTaxIncluded,
        int $roundType,
        int $computingPrecision
    ): void {
        $floatPriceTaxExcluded = (float) (string) $priceTaxExcluded;
        $floatPriceTaxIncluded = (float) (string) $priceTaxIncluded;

        $orderDetail->product_price = $orderDetail->unit_price_tax_excl = $floatPriceTaxExcluded;
        $orderDetail->unit_price_tax_incl = $floatPriceTaxIncluded;
        switch ($roundType) {
            case Order::ROUND_TOTAL:
                $orderDetail->total_price_tax_excl = $floatPriceTaxExcluded * $orderDetail->product_quantity;
                $orderDetail->total_price_tax_incl = $floatPriceTaxIncluded * $orderDetail->product_quantity;

                break;
            case Order::ROUND_LINE:
                $orderDetail->total_price_tax_excl = Tools::ps_round($floatPriceTaxExcluded * $orderDetail->product_quantity, $computingPrecision);
                $orderDetail->total_price_tax_incl = Tools::ps_round($floatPriceTaxIncluded * $orderDetail->product_quantity, $computingPrecision);

                break;

            case Order::ROUND_ITEM:
            default:
                $orderDetail->product_price = $orderDetail->unit_price_tax_excl = Tools::ps_round($floatPriceTaxExcluded, $computingPrecision);
                $orderDetail->unit_price_tax_incl = Tools::ps_round($floatPriceTaxIncluded, $computingPrecision);
                $orderDetail->total_price_tax_excl = $orderDetail->unit_price_tax_excl * $orderDetail->product_quantity;
                $orderDetail->total_price_tax_incl = $orderDetail->unit_price_tax_incl * $orderDetail->product_quantity;

                break;
        }

        if (!$orderDetail->update()) {
            throw new OrderException('An error occurred while editing the product line.');
        }
    }

    /**
     * @param Order $order
     * @param int $productId
     * @param int $combinationId
     * @param DecimalNumber $priceTaxExcluded
     * @param DecimalNumber $priceTaxIncluded
     * @param int $roundType
     * @param int $computingPrecision
     * @param Address $taxAddress
     *
     * @throws OrderException
     */
    private function applyUpdatesForProduct(
        Order $order,
        int $productId,
        int $combinationId,
        DecimalNumber $priceTaxExcluded,
        DecimalNumber $priceTaxIncluded,
        int $roundType,
        int $computingPrecision,
        Address $taxAddress
    ): void {
        $identicalOrderDetails = $this->getOrderDetailsForProduct($order, $productId, $combinationId);
        if (empty($identicalOrderDetails)) {
            return;
        }

        // Get precise prices thanks to first OrderDetail (they all have the same price anyway)
        $orderDetail = $identicalOrderDetails[0];
        $ecotax = new DecimalNumber($orderDetail->ecotax);

        $ecotaxTaxCalculator = $this->getTaxCalculatorForEcotax($taxAddress);
        $ecotaxTaxFactor = new DecimalNumber((string) (1 + ($ecotaxTaxCalculator->getTotalRate() / 100)));
        $ecotaxTaxIncluded = $ecotax->times($ecotaxTaxFactor);

        // Prices coming from the backoffice : they are display with ecotax
        // So we need to remove ecotax before having precise price
        $priceTaxExcluded = $priceTaxExcluded->minus($ecotax);
        $priceTaxIncluded = $priceTaxIncluded->minus($ecotaxTaxIncluded);

        $precisePriceTaxExcluded = $this->getPrecisePriceTaxExcluded($priceTaxIncluded, $priceTaxExcluded, $order, $orderDetail, $taxAddress);
        $precisePriceTaxIncluded = $this->getPrecisePriceTaxIncluded($priceTaxIncluded, $priceTaxExcluded, $order, $orderDetail, $taxAddress);

        $precisePriceTaxExcluded = $precisePriceTaxExcluded->plus($ecotax);
        $precisePriceTaxIncluded = $precisePriceTaxIncluded->plus($ecotaxTaxIncluded);

        foreach ($identicalOrderDetails as $identicalOrderDetail) {
            $this->applyOrderDetailPriceUpdate(
                $identicalOrderDetail,
                $precisePriceTaxExcluded,
                $precisePriceTaxIncluded,
                $roundType,
                $computingPrecision
            );
        }
    }

    /**
     * @param Order $order
     * @param int $productId
     * @param int $combinationId
     *
     * @return array
     */
    private function getOrderDetailsForProduct(
        Order $order,
        int $productId,
        int $combinationId
    ): array {
        $identicalOrderDetails = [];
        $orderDetails = $order->getOrderDetailList();
        foreach ($orderDetails as $orderDetail) {
            if ((int) $orderDetail['product_id'] === $productId
                && (int) $orderDetail['product_attribute_id'] === $combinationId) {
                $identicalOrderDetails[] = new OrderDetail($orderDetail['id_order_detail']);
            }
        }

        return $identicalOrderDetails;
    }

    /**
     * Since prices in input are sometimes rounded they don't precisely match, so in this case
     * if the price is different from catalog we use price included as a base and recompute the
     * price tax excluded with additional precision.
     *
     * @param DecimalNumber $priceTaxIncluded
     * @param DecimalNumber $priceTaxExcluded
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param Address $taxAddress
     *
     * @return DecimalNumber
     */
    private function getPrecisePriceTaxExcluded(
        DecimalNumber $priceTaxIncluded,
        DecimalNumber $priceTaxExcluded,
        Order $order,
        OrderDetail $orderDetail,
        Address $taxAddress
    ): DecimalNumber {
        $productOriginalPrice = $this->getProductRegularPrice($order, $orderDetail, $taxAddress);

        // If provided price is equal to catalog price no need to recompute
        if ($productOriginalPrice->equals($priceTaxExcluded)) {
            return $priceTaxExcluded;
        }

        $productTaxCalculator = $this->getTaxCalculatorByAddress($taxAddress, $orderDetail);
        $taxFactor = new DecimalNumber((string) (1 + ($productTaxCalculator->getTotalRate() / 100)));

        $computedPriceTaxIncluded = $priceTaxExcluded->times($taxFactor);
        if ($computedPriceTaxIncluded->equals($priceTaxIncluded)) {
            return $priceTaxExcluded;
        }

        // When price tax included is computed based on price tax excluded there is a difference
        // so we recompute the price tax excluded based on the tax rate to have more precision
        return $priceTaxIncluded->dividedBy($taxFactor);
    }

    /**
     * Since prices in input are sometimes rounded they don't precisely match, so in this case
     * if the price is the same as the catalog we use price excluded as a base and recompute the
     * price tax included with additional precision.
     *
     * @param DecimalNumber $priceTaxIncluded
     * @param DecimalNumber $priceTaxExcluded
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param Address $taxAddress
     *
     * @return DecimalNumber
     */
    private function getPrecisePriceTaxIncluded(
        DecimalNumber $priceTaxIncluded,
        DecimalNumber $priceTaxExcluded,
        Order $order,
        OrderDetail $orderDetail,
        Address $taxAddress
    ): DecimalNumber {
        $productOriginalPrice = $this->getProductRegularPrice($order, $orderDetail, $taxAddress);

        // If provided price is different from the catalog price we use the input price tax included as a base
        if (!$productOriginalPrice->equals($priceTaxExcluded)) {
            return $priceTaxIncluded;
        }

        $productTaxCalculator = $this->getTaxCalculatorByAddress($taxAddress, $orderDetail);
        $taxFactor = new DecimalNumber((string) (1 + ($productTaxCalculator->getTotalRate() / 100)));

        return $priceTaxExcluded->times($taxFactor);
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param Address $taxAddress
     *
     * @return DecimalNumber
     */
    private function getProductRegularPrice(
        Order $order,
        OrderDetail $orderDetail,
        Address $taxAddress
    ): DecimalNumber {
        // Get price via getPriceStatic so that the catalog price rules are applied
        $null = null;

        return new DecimalNumber((string) Product::getPriceStatic(
            (int) $orderDetail->product_id,
            false,
            (int) $orderDetail->product_attribute_id,
            self::COMPARISON_PRECISION,
            null,
            false,
            true,
            1,
            false,
            $order->id_customer, // We still use the customer ID in case this customer has some special prices
            null, // But we keep the cart null as we don't want this order overridden price
            $taxAddress->id,
            $null,
            false
        ));
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

    /**
     * Get a TaxCalculator adapted for Ecotax
     *
     * @param Address $address
     *
     * @return TaxCalculator
     */
    private function getTaxCalculatorForEcotax(Address $address): TaxCalculator
    {
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            (int) $this->shopConfiguration->get('PS_ECOTAX_TAX_RULES_GROUP_ID')
        );

        return $tax_manager->getTaxCalculator();
    }
}
