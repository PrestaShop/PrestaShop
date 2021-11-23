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

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;
use Currency;
use Tools;

class Fees
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var AmountImmutable
     */
    protected $shippingFees;

    /**
     * @var AmountImmutable|null
     */
    protected $finalShippingFees;

    /**
     * @var AmountImmutable
     */
    protected $wrappingFees;

    /**
     * @var AmountImmutable
     */
    protected $finalWrappingFees;

    /**
     * indicates if cart was already processed.
     *
     * @var bool
     */
    protected $isProcessed = false;

    /**
     * @var int|null
     */
    protected $orderId;

    /**
     * @param int|null $orderId
     */
    public function __construct(?int $orderId = null)
    {
        $this->shippingFees = new AmountImmutable();
        $this->orderId = $orderId;
    }

    /**
     * @param Cart $cart
     * @param CartRowCollection $cartRowCollection
     * @param int $computePrecision
     * @param int|null $id_carrier
     */
    public function processCalculation(
        Cart $cart,
        CartRowCollection $cartRowCollection,
        $computePrecision,
        $id_carrier = null
    ) {
        if ($id_carrier === null) {
            $this->shippingFees = new AmountImmutable(
                $cart->getTotalShippingCost(null, true),
                $cart->getTotalShippingCost(null, false)
            );
        } else {
            $products = $cartRowCollection->getProducts();
            $this->shippingFees = new AmountImmutable(
                $cart->getPackageShippingCost(
                    (int) $id_carrier,
                    true,
                    null,
                    $products,
                    null,
                    null !== $this->orderId
                ),
                $cart->getPackageShippingCost(
                    (int) $id_carrier,
                    false,
                    null,
                    $products,
                    null,
                    null !== $this->orderId
                )
            );
        }
        $this->finalShippingFees = clone $this->shippingFees;

        // wrapping fees
        if ($cart->gift) {
            $this->wrappingFees = new AmountImmutable(
                Tools::convertPrice(
                    Tools::ps_round(
                        $cart->getGiftWrappingPrice(true),
                        $computePrecision
                    ),
                    Currency::getCurrencyInstance((int) $cart->id_currency)
                ),
                Tools::convertPrice(
                    Tools::ps_round(
                        $cart->getGiftWrappingPrice(false),
                        $computePrecision
                    ),
                    Currency::getCurrencyInstance((int) $cart->id_currency)
                )
            );
        } else {
            $this->wrappingFees = new AmountImmutable();
        }
        $this->finalWrappingFees = clone $this->wrappingFees;
        $this->isProcessed = true;
    }

    /**
     * @param Cart $cart
     *
     * @return Fees
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return AmountImmutable
     */
    public function getInitialShippingFees()
    {
        return $this->shippingFees;
    }

    /**
     * @return AmountImmutable|null
     */
    public function getFinalShippingFees()
    {
        return $this->finalShippingFees;
    }

    /**
     * @return AmountImmutable
     */
    public function getFinalWrappingFees()
    {
        return $this->finalWrappingFees;
    }

    /**
     * @return AmountImmutable
     */
    public function getInitialWrappingFees()
    {
        return $this->wrappingFees;
    }

    public function subDiscountValueShipping(AmountImmutable $amount)
    {
        $taxIncluded = $this->finalShippingFees->getTaxIncluded() - $amount->getTaxIncluded();
        $taxExcluded = $this->finalShippingFees->getTaxExcluded() - $amount->getTaxExcluded();
        if ($taxIncluded < 0) {
            $taxIncluded = 0;
        }
        if ($taxExcluded < 0) {
            $taxExcluded = 0;
        }
        $this->finalShippingFees = new AmountImmutable(
            $taxIncluded,
            $taxExcluded
        );
    }
}
