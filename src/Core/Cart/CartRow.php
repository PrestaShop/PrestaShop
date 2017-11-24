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

use PrestaShop\PrestaShop\Adapter\ServiceLocator;

class CartRow
{
    protected $rowData = array();

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

    public function getInitialUnitPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->initialUnitPrice;
    }

    public function getFinalUnitPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->finalUnitPrice;
    }

    public function getFinalTotalPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->finalTotalPrice;
    }

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
        $this->finalUnitPrice   = clone($this->initialUnitPrice);
        $this->finalTotalPrice  = new Amount(
            $this->initialUnitPrice->getTaxIncluded() * $quantity,
            $this->initialUnitPrice->getTaxExcluded() * $quantity
        );

        // store state
        $this->isProcessed = true;
    }

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
        $amount = new Amount($discountTaxIncluded, $discountTaxExcluded);
        $this->subDiscountAmount($amount);

        return $amount;
    }

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
