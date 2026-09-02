<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;
use CartCore;
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
     * @param CartCore $cart
     * @param CartRowCollection $cartRowCollection
     * @param int $computePrecision
     * @param int|null $id_carrier
     */
    public function processCalculation(
        CartCore $cart,
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
