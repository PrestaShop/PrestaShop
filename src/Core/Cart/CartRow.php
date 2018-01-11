<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;
use Context;
use Order;
use PrestaShop\PrestaShop\Adapter\Product\PriceCalculator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Tools;

/**
 * represent a cart row, ie a product and a quantity, and some post-process data like cart rule applied
 */
class CartRow
{
    /**
     * @var PriceCalculator
     */
    protected $priceCalculator;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var array previous data for product: array given by Cart::getProducts()
     */
    protected $rowData = [];

    /**
     * @var AmountImmutable
     */
    protected $initialUnitPrice;

    /**
     * @var AmountImmutable
     */
    protected $finalUnitPrice;

    /**
     * @var AmountImmutable
     */
    protected $finalTotalPrice;

    /**
     * @var bool indicates if the calculation was triggered (reset on data changes)
     */
    protected $isProcessed = false;

    /**
     * @param array $rowData array item given by Cart::getProducts()
     * @param PriceCalculator $priceCalculator
     * @param ConfigurationInterface $configuration
     */
    public function __construct($rowData, PriceCalculator $priceCalculator, ConfigurationInterface $configuration)
    {
        $this->setRowData($rowData);
        $this->priceCalculator = $priceCalculator;
        $this->configuration   = $configuration;
    }

    /**
     * @param array $rowData
     *
     * @return CartRow
     */
    public function setRowData($rowData)
    {
        $this->rowData = $rowData;

        return $this;
    }

    /**
     * @return array
     */
    public function getRowData()
    {
        return $this->rowData;
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Cart\AmountImmutable
     * @throws \Exception
     */
    public function getInitialUnitPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->initialUnitPrice;
    }

    /**
     * return final price: initial minus the cart rule discounts
     *
     * @return \PrestaShop\PrestaShop\Core\Cart\AmountImmutable
     * @throws \Exception
     */
    public function getFinalUnitPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->finalUnitPrice;
    }

    /**
     * return final price: initial minus the cart rule discounts
     *
     * @return \PrestaShop\PrestaShop\Core\Cart\AmountImmutable
     * @throws \Exception
     */
    public function getFinalTotalPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->finalTotalPrice;
    }

    /**
     * run initial row calculation
     *
     * @param Cart $cart
     *
     * @throws \PrestaShop\PrestaShop\Adapter\CoreException
     */
    public function processCalculation(Cart $cart)
    {
        $rowData                = $this->getRowData();
        $quantity               = (int) $rowData['cart_quantity'];
        $this->initialUnitPrice = $this->getProductPrice($cart, $rowData);
        // store not rounded values
        $this->finalTotalPrice = new AmountImmutable(
            $this->initialUnitPrice->getTaxIncluded() * $quantity,
            $this->initialUnitPrice->getTaxExcluded() * $quantity
        );
        $this->applyRound();
        // store state
        $this->isProcessed = true;
    }

    protected function getProductPrice(Cart $cart, $rowData)
    {
        $userEcotax    = $this->configuration->get('PS_USE_ECOTAX');

        // Define virtual context to prevent case where the cart is not the in the global context
        $virtualContext       = Context::getContext()->cloneContext();
        $virtualContext->cart = $cart;

        $addressId = $cart->getProductAddressId($rowData);

        // The $null variable below is not used,
        // but it is necessary to pass it to getProductPrice because
        // it expects a reference.
        $specificPriceOutput = null;

        $quantity = (int) $rowData['cart_quantity'];

        return new AmountImmutable(
            $this->priceCalculator->getProductPrice(
                (int) $rowData['id_product'],
                true,
                (int) $rowData['id_product_attribute'],
                6,
                null,
                false,
                true,
                $quantity,
                false,
                (int) $cart->id_customer ? (int) $cart->id_customer : null,
                (int) $cart->id,
                $addressId,
                $specificPriceOutput,
                $userEcotax,
                true,
                $virtualContext,
                true,
                (int) $rowData['id_customization']
            ),
            $this->priceCalculator->getProductPrice(
                (int) $rowData['id_product'],
                false,
                (int) $rowData['id_product_attribute'],
                6,
                null,
                false,
                true,
                $quantity,
                false,
                (int) $cart->id_customer ? (int) $cart->id_customer : null,
                (int) $cart->id,
                $addressId,
                $specificPriceOutput,
                $userEcotax,
                true,
                $virtualContext,
                true,
                (int) $rowData['id_customization']
            )
        );
    }

    protected function applyRound()
    {
        // ROUNDING MODE
        $this->finalUnitPrice = clone($this->initialUnitPrice);

        $rowData  = $this->getRowData();
        $quantity = (int) $rowData['cart_quantity'];
        $precision     = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        switch ($this->configuration->get('PS_ROUND_TYPE')) {
            case Order::ROUND_TOTAL:
                // do not round the line
                $this->finalTotalPrice = new AmountImmutable(
                    $this->initialUnitPrice->getTaxIncluded() * $quantity,
                    $this->initialUnitPrice->getTaxExcluded() * $quantity
                );
                break;
            case Order::ROUND_LINE:
                // round line result
                $this->finalTotalPrice = new AmountImmutable(
                    Tools::ps_round($this->initialUnitPrice->getTaxIncluded() * $quantity, $precision),
                    Tools::ps_round($this->initialUnitPrice->getTaxExcluded() * $quantity, $precision)
                );
                break;

            case Order::ROUND_ITEM:
            default:
                // round each item
                $this->initialUnitPrice = new AmountImmutable(
                    Tools::ps_round($this->initialUnitPrice->getTaxIncluded(), $precision),
                    Tools::ps_round($this->initialUnitPrice->getTaxExcluded(), $precision)
                );
                $this->finalTotalPrice  = new AmountImmutable(
                    $this->initialUnitPrice->getTaxIncluded() * $quantity,
                    $this->initialUnitPrice->getTaxExcluded() * $quantity
                );
                break;
        }
    }

    /**
     * substract discount from the row
     * if discount exceeds amount, we keep 0 (no use of negative amounts)
     *
     * @param \PrestaShop\PrestaShop\Core\Cart\AmountImmutable $amount
     */
    public function applyFlatDiscount(AmountImmutable $amount)
    {
        $taxIncluded = $this->finalTotalPrice->getTaxIncluded() - $amount->getTaxIncluded();
        $taxExcluded = $this->finalTotalPrice->getTaxExcluded() - $amount->getTaxExcluded();
        if ($taxIncluded < 0) {
            $taxIncluded = 0;
        }
        if ($taxExcluded < 0) {
            $taxExcluded = 0;
        }
        $this->finalTotalPrice = new AmountImmutable(
            $taxIncluded,
            $taxExcluded
        );

        $this->updateFinalUnitPrice();
    }

    /**
     * @param float $percent 0-100
     *
     * @return AmountImmutable
     */
    public function applyPercentageDiscount($percent)
    {
        $percent = (float) $percent;
        if ($percent < 0 || $percent > 100) {
            throw new \Exception('Invalid percentage discount given: ' . $percent);
        }
        $discountTaxIncluded = $this->finalTotalPrice->getTaxIncluded() * $percent / 100;
        $discountTaxExcluded = $this->finalTotalPrice->getTaxExcluded() * $percent / 100;
        $amount              = new AmountImmutable($discountTaxIncluded, $discountTaxExcluded);
        $this->applyFlatDiscount($amount);

        return $amount;
    }

    /**
     * when final row price is calculated, we need to update unit price
     */
    protected function updateFinalUnitPrice()
    {
        $rowData              = $this->getRowData();
        $quantity             = (int) $rowData['cart_quantity'];
        $taxIncluded          = $this->finalTotalPrice->getTaxIncluded();
        $taxExcluded          = $this->finalTotalPrice->getTaxExcluded();
        $this->finalUnitPrice = new AmountImmutable(
            $taxIncluded / $quantity,
            $taxExcluded / $quantity
        );
    }
}
