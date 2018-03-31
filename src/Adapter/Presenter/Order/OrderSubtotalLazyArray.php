<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShopBundle\Translation\TranslatorComponent;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Cart;
use Configuration;
use Context;
use HistoryController;
use Order;
use TaxConfiguration;
use Currency;

class OrderSubtotalLazyArray extends AbstractLazyArray
{
    /** @var Order */
    private $order;

    /** @var Context */
    private $context;

    /* @var TaxConfiguration */
    private $taxConfiguration;

    /* @var PriceFormatter */
    private $priceFormatter;

    /* @var TranslatorComponent */
    private $translator;

    /**
     * OrderSubtotalLazyArray constructor.
     */
    public function __construct($order)
    {
        $this->context = Context::getContext();
        $this->taxConfiguration = new TaxConfiguration();
        $this->priceFormatter = new PriceFormatter();
        $this->translator = Context::getContext()->getTranslator();
        $this->order = $order;
        parent::__construct();
    }

    /**
     * @arrayAccess
     * @return array
     */
    public function getProducts()
    {
        $totalProducts = ($this->includeTaxes()) ? $this->order->total_products_wt : $this->order->total_products;
        return array(
            'type' => 'products',
            'label' => $this->translator->trans('Subtotal', array(), 'Shop.Theme.Checkout'),
            'amount' => $totalProducts,
            'value' => $this->priceFormatter->format(
                $totalProducts,
                Currency::getCurrencyInstance((int)$this->order->id_currency)
            ),
        );
    }

    /**
     * @arrayAccess
     * @return array
     */
    public function getDiscounts()
    {
        $discountAmount = ($this->includeTaxes())
            ? $this->order->total_discounts_tax_incl
            : $this->order->total_discounts_tax_excl;
        if ((float) $discountAmount) {
            return array(
                'type' => 'discount',
                'label' => $this->translator->trans('Discount', array(), 'Shop.Theme.Checkout'),
                'amount' => $discountAmount,
                'value' => $this->priceFormatter->format(
                    $discountAmount,
                    Currency::getCurrencyInstance((int)$this->order->id_currency)
                ),
            );
        }

        return array(
            'type' => 'discount',
            'label' => null,
            'amount' => null,
            'value' => ''
        );
    }

    /**
     * @arrayAccess
     * @return array
     */
    public function getShipping()
    {
        $cart = new Cart($this->order->id_cart);
        if (!$cart->isVirtualCart()) {
            $shippingCost = ($this->includeTaxes())
                ? $this->order->total_shipping_tax_incl : $this->order->total_shipping_tax_excl;
            return array(
                'type' => 'shipping',
                'label' => $this->translator->trans('Shipping and handling', array(), 'Shop.Theme.Checkout'),
                'amount' => $shippingCost,
                'value' => $shippingCost != 0 ? $this->priceFormatter->format(
                    $shippingCost,
                    Currency::getCurrencyInstance((int)$this->order->id_currency)
                )
                    : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout'),
            );
        }

        return array(
            'type' => 'shipping',
            'label' => null,
            'amount' => null,
            'value' => ''
        );
    }

    /**
     * @arrayAccess
     * @return array
     */
    public function getTax()
    {
        $tax = $this->order->total_paid_tax_incl - $this->order->total_paid_tax_excl;
        if ((float) $tax && Configuration::get('PS_TAX_DISPLAY')) {
            return array(
                'type' => 'tax',
                'label' => $this->translator->trans('Tax', array(), 'Shop.Theme.Checkout'),
                'amount' => $tax,
                'value' => $this->priceFormatter->format(
                    $tax,
                    Currency::getCurrencyInstance((int)$this->order->id_currency)
                ),
            );
        }

        return array(
            'type' => 'tax',
            'label' => null,
            'amount' => null,
            'value' => '',
        );
    }

    /**
     * @arrayAccess
     * @return array
     */
    public function getGiftWrapping()
    {
        if ($this->order->gift) {
            $giftWrapping = ($this->includeTaxes())
                ? $this->order->total_wrapping_tax_incl
                : $this->order->total_wrapping_tax_excl;
            return array(
                'type' => 'gift_wrapping',
                'label' => $this->translator->trans('Gift wrapping', array(), 'Shop.Theme.Checkout'),
                'amount' => $giftWrapping,
                'value' => $this->priceFormatter->format(
                    $giftWrapping,
                    Currency::getCurrencyInstance((int)$this->order->id_currency)
                ),
            );
        }

        return array(
            'type' => 'gift_wrapping',
            'label' => null,
            'amount' => null,
            'value' => '',
        );
    }


    /**
     * @return bool|mixed
     */
    private function includeTaxes()
    {
        return $this->taxConfiguration->includeTaxes();
    }
}
