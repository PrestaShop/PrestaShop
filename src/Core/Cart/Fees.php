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

class Fees
{

    /**
     * @var \CartCore
     */
    protected $cart;

    /**
     * @var Amount
     */
    protected $shippingFees;

    /**
     * @var Amount
     */
    protected $finalShippingFees;

    /**
     * @var Amount
     */
    protected $wrappingFees;

    /**
     * @var Amount
     */
    protected $finalWrappingFees;

    /**
     * indicates if cart was already processed
     *
     * @var bool
     */
    protected $isProcessed = false;

    public function processCalculation(
        \CartCore $cart,
        \PrestaShop\PrestaShop\Core\Cart\CartRowCollection $cartRowCollection,
        $id_carrier = null
    ) {
        if (!count($cartRowCollection) && $id_carrier === null) {
            $this->shippingFees = new Amount(
                $cart->getTotalShippingCost(null, true),
                $cart->getTotalShippingCost(null, false)
            );
        } else {
            $products = $cartRowCollection->getProducts();
            $this->shippingFees = new Amount(
                $cart->getPackageShippingCost(
                    (int) $id_carrier,
                    true,
                    null,
                    $products
                ),
                $cart->getPackageShippingCost(
                    (int) $id_carrier,
                    false,
                    null,
                    $products
                )
            );
        }
        $this->finalShippingFees = clone($this->shippingFees);

        // wrapping fees
        $configuration           = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');
        $computePrecision        = $configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        $this->wrappingFees      = new Amount(
            \Tools::convertPrice(
                \Tools::ps_round(
                    $cart->getGiftWrappingPrice(true),
                    $computePrecision
                ),
                \Currency::getCurrencyInstance((int) $cart->id_currency)
            ),
            \Tools::convertPrice(
                \Tools::ps_round(
                    $cart->getGiftWrappingPrice(false),
                    $computePrecision
                ),
                \Currency::getCurrencyInstance((int) $cart->id_currency)
            )
        );
        $this->finalWrappingFees = clone($this->wrappingFees);

        $this->isProcessed = true;
    }

    /**
     * @param \CartCore $cart
     *
     * @return Fees
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Amount
     */
    public function getInitialShippingFees()
    {
        return $this->shippingFees;
    }

    /**
     * @return Amount
     */
    public function getFinalShippingFees()
    {
        return $this->finalShippingFees;
    }

    /**
     * @return Amount
     */
    public function getFinalWrappingFees()
    {
        return $this->finalShippingFees;
    }

    /**
     * @return Amount
     */
    public function getInitialWrappingFees()
    {
        return $this->finalWrappingFees;
    }

    public function subDiscountValueShipping(Amount $amount)
    {
        $taxIncluded = $this->finalShippingFees->getTaxIncluded() - $amount->getTaxIncluded();
        $taxExcluded = $this->finalShippingFees->getTaxExcluded() - $amount->getTaxExcluded();
        if ($taxIncluded < 0) {
            $taxIncluded = 0;
        }
        if ($taxExcluded < 0) {
            $taxExcluded = 0;
        }
        $this->finalShippingFees->setTaxIncluded($taxIncluded);
        $this->finalShippingFees->setTaxExcluded($taxExcluded);
    }

}
