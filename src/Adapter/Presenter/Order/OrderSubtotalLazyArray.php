<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Cart;
use Configuration;
use Context;
use Currency;
use Order;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;
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
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
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
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
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
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
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
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
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
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
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
