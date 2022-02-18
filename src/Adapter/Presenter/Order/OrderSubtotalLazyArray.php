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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Cart;
use Configuration;
use Context;
use Currency;
use Order;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShopBundle\Translation\TranslatorComponent;
use TaxConfiguration;

class OrderSubtotalLazyArray extends AbstractLazyArray
{
    /** @var Order */
    private $order;

    /** @var TaxConfiguration */
    private $taxConfiguration;

    /** @var PriceFormatter */
    private $priceFormatter;

    /** @var bool */
    private $includeTaxes;

    /** @var TranslatorComponent */
    private $translator;

    /**
     * OrderSubtotalLazyArray constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->taxConfiguration = new TaxConfiguration();
        $this->includeTaxes = $this->includeTaxes();
        $this->priceFormatter = new PriceFormatter();
        $this->translator = Context::getContext()->getTranslator();
        $this->order = $order;
        parent::__construct();
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getProducts()
    {
        $totalProducts = ($this->includeTaxes) ? $this->order->total_products_wt : $this->order->total_products;

        return [
            'type' => 'products',
            'label' => $this->translator->trans('Subtotal', [], 'Shop.Theme.Checkout'),
            'amount' => $totalProducts,
            'value' => $this->priceFormatter->format(
                $totalProducts,
                Currency::getCurrencyInstance((int) $this->order->id_currency)
            ),
        ];
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getDiscounts()
    {
        $discountAmount = ($this->includeTaxes)
            ? $this->order->total_discounts_tax_incl
            : $this->order->total_discounts_tax_excl;
        if ((float) $discountAmount) {
            return [
                'type' => 'discount',
                'label' => $this->translator->trans('Discount', [], 'Shop.Theme.Checkout'),
                'amount' => $discountAmount,
                'value' => $this->priceFormatter->format(
                    $discountAmount,
                    Currency::getCurrencyInstance((int) $this->order->id_currency)
                ),
            ];
        }

        return [
            'type' => 'discount',
            'label' => null,
            'amount' => null,
            'value' => '',
        ];
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getShipping()
    {
        $cart = new Cart($this->order->id_cart);
        if (!$cart->isVirtualCart()) {
            $shippingCost = ($this->includeTaxes)
                ? $this->order->total_shipping_tax_incl : $this->order->total_shipping_tax_excl;

            return [
                'type' => 'shipping',
                'label' => $this->translator->trans('Shipping and handling', [], 'Shop.Theme.Checkout'),
                'amount' => $shippingCost,
                'value' => $shippingCost != 0 ? $this->priceFormatter->format(
                    $shippingCost,
                    Currency::getCurrencyInstance((int) $this->order->id_currency)
                )
                    : $this->translator->trans('Free', [], 'Shop.Theme.Checkout'),
            ];
        }

        return [
            'type' => 'shipping',
            'label' => null,
            'amount' => null,
            'value' => '',
        ];
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getTax()
    {
        if (!Configuration::get('PS_TAX_DISPLAY')) {
            return [
                'type' => 'tax',
                'label' => null,
                'amount' => null,
                'value' => '',
            ];
        }

        $tax = $this->order->total_paid_tax_incl - $this->order->total_paid_tax_excl;

        return [
            'type' => 'tax',
            'label' => $this->translator->trans('Tax', [], 'Shop.Theme.Checkout'),
            'amount' => $tax,
            'value' => $this->priceFormatter->format(
                $tax,
                Currency::getCurrencyInstance((int) $this->order->id_currency)
            ),
        ];
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getGiftWrapping()
    {
        if ($this->order->gift) {
            $giftWrapping = ($this->includeTaxes)
                ? $this->order->total_wrapping_tax_incl
                : $this->order->total_wrapping_tax_excl;

            return [
                'type' => 'gift_wrapping',
                'label' => $this->translator->trans('Gift wrapping', [], 'Shop.Theme.Checkout'),
                'amount' => $giftWrapping,
                'value' => $this->priceFormatter->format(
                    $giftWrapping,
                    Currency::getCurrencyInstance((int) $this->order->id_currency)
                ),
            ];
        }

        return [
            'type' => 'gift_wrapping',
            'label' => null,
            'amount' => null,
            'value' => '',
        ];
    }

    /**
     * @return bool
     */
    private function includeTaxes()
    {
        return $this->taxConfiguration->includeTaxes();
    }
}
