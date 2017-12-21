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

use Order;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Tools;

/**
 * represent a cart row, ie a product and a quantity, and some post-process data like cart rule applied
 */
class CartRow
{
    protected $rowData = [];

    /**
     * @var Amount
     */
    protected $initialUnitPrice;

    /**
     * @var Amount
     */
    protected $finalUnitPrice;

    /**
     * @var Amount
     */
    protected $finalTotalPrice;

    protected $isProcessed = false;

    public function __construct($rowData)
    {
        $this->setRowData($rowData);
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
     * @return \PrestaShop\PrestaShop\Core\Cart\Amount
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
     * @return \PrestaShop\PrestaShop\Core\Cart\Amount
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
     * @return \PrestaShop\PrestaShop\Core\Cart\Amount
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
     * @param \CartCore $cart
     *
     * @throws \PrestaShop\PrestaShop\Adapter\CoreException
     */
    public function processCalculation(\CartCore $cart)
    {
        /** @var \PrestaShop\PrestaShop\Adapter\Product\PriceCalculator $price_calculator */
        $price_calculator = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PriceCalculator');
        /** @var \PrestaShop\PrestaShop\Core\ConfigurationInterface $configuration */
        $configuration = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');

        $ps_use_ecotax = $configuration->get('PS_USE_ECOTAX');

        $rowData = $this->getRowData();

        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context       = \Context::getContext()->cloneContext();
        $virtual_context->cart = $cart;

        $id_address = $cart->getProductAddressId($rowData);

        // The $null variable below is not used,
        // but it is necessary to pass it to getProductPrice because
        // it expects a reference.
        $null                   = null;
        $quantity               = (int) $rowData['cart_quantity'];
        $this->initialUnitPrice = new Amount(
            $price_calculator->getProductPrice(
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
                $id_address,
                $null,
                $ps_use_ecotax,
                true,
                $virtual_context,
                true,
                (int) $rowData['id_customization']
            ),
            $price_calculator->getProductPrice(
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
                $id_address,
                $null,
                $ps_use_ecotax,
                true,
                $virtual_context,
                true,
                (int) $rowData['id_customization']
            )
        );
        // store not rounded values
        $this->finalTotalPrice = new Amount(
            $this->initialUnitPrice->getTaxIncluded() * $quantity,
            $this->initialUnitPrice->getTaxExcluded() * $quantity
        );
        $this->applyRound();
        // store state
        $this->isProcessed = true;
    }

    protected function applyRound()
    {
        // ROUNDING MODE
        $this->finalUnitPrice = clone($this->initialUnitPrice);

        $rowData  = $this->getRowData();
        $quantity = (int) $rowData['cart_quantity'];
        /** @var \PrestaShop\PrestaShop\Core\ConfigurationInterface $configuration */
        $configuration = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');
        $precision     = $configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        switch ($configuration->get('PS_ROUND_TYPE')) {
            case Order::ROUND_TOTAL:
                // do not round the line
                $this->finalTotalPrice = new Amount(
                    $this->initialUnitPrice->getTaxIncluded() * $quantity,
                    $this->initialUnitPrice->getTaxExcluded() * $quantity
                );
                break;
            case Order::ROUND_LINE:
                // round line result
                $this->finalTotalPrice = new Amount(
                    Tools::ps_round($this->initialUnitPrice->getTaxIncluded() * $quantity, $precision),
                    Tools::ps_round($this->initialUnitPrice->getTaxExcluded() * $quantity, $precision)
                );
                break;

            case Order::ROUND_ITEM:
            default:
                // round each item
                $this->initialUnitPrice->setTaxExcluded(
                    Tools::ps_round($this->initialUnitPrice->getTaxExcluded(), $precision)
                );
                $this->initialUnitPrice->setTaxIncluded(
                    Tools::ps_round($this->initialUnitPrice->getTaxIncluded(), $precision)
                );
                $this->finalTotalPrice = new Amount(
                    $this->initialUnitPrice->getTaxIncluded() * $quantity,
                    $this->initialUnitPrice->getTaxExcluded() * $quantity
                );
                break;
        }
    }

    /**
     * substract discount from the row
     *
     * @param \PrestaShop\PrestaShop\Core\Cart\Amount $amount
     */
    public function subDiscountAmount(Amount $amount)
    {
        $taxIncluded = $this->finalTotalPrice->getTaxIncluded() - $amount->getTaxIncluded();
        $taxExcluded = $this->finalTotalPrice->getTaxExcluded() - $amount->getTaxExcluded();
        if ($taxIncluded < 0) {
            $taxIncluded = 0;
        }
        if ($taxExcluded < 0) {
            $taxExcluded = 0;
        }
        $this->finalTotalPrice->setTaxIncluded($taxIncluded);
        $this->finalTotalPrice->setTaxExcluded($taxExcluded);

        $this->updateFinalUnitPrice();
    }

    /**
     * @param float $percent 0-100
     *
     * @return Amount
     */
    public function subDiscountPercent($percent)
    {
        $discountTaxIncluded = $this->finalTotalPrice->getTaxIncluded() * $percent / 100;
        $discountTaxExcluded = $this->finalTotalPrice->getTaxExcluded() * $percent / 100;
        $amount              = new Amount($discountTaxIncluded, $discountTaxExcluded);
        $this->subDiscountAmount($amount);

        return $amount;
    }

    /**
     * when final row price is calculated, we need to update unit price
     */
    protected function updateFinalUnitPrice()
    {
        $rowData     = $this->getRowData();
        $quantity    = (int) $rowData['cart_quantity'];
        $taxIncluded = $this->finalTotalPrice->getTaxIncluded();
        $taxExcluded = $this->finalTotalPrice->getTaxExcluded();
        $this->finalUnitPrice->setTaxIncluded($taxIncluded / $quantity);
        $this->finalUnitPrice->setTaxExcluded($taxExcluded / $quantity);
    }
}
