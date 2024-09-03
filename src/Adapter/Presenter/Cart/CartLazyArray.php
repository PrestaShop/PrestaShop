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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Cart;

use Cart;
use CartRule;
use Configuration;
use Context;
use Country;
use Hook;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

class CartLazyArray extends AbstractLazyArray
{
    /**
     * @var bool
     */
    private $shouldSeparateGifts;

    /**
     * @var CartPresenter
     */
    private $cartPresenter;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PriceFormatter
     */
    private $priceFormatter;

    /**
     * @var array|null
     */
    private $products;

    /**
     * @var array
     */
    private $totals;

    /**
     * @var array
     */
    private $subTotals;

    /**
     * @var string
     */
    private $summaryString;

    /**
     * @var int
     */
    private $productsCount;

    /**
     * @var array
     */
    private $vouchers;

    /**
     * @var float
     */
    private $minimalPurchase;

    /**
     * @var string
     */
    private $minimalPurchaseRequired;

    /**
     * @var array
     */
    private $labels;

    /**
     * @var int
     */
    private $idAddressDelivery;

    /**
     * @var int
     */
    private $idAddressInvoice;

    /**
     * @var bool
     */
    private $isVirtual;

    /**
     * @var array
     */
    private $discounts;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    public function __construct(Cart $cart, CartPresenter $cartPresenter, bool $shouldSeparateGifts = false)
    {
        $this->shouldSeparateGifts = $shouldSeparateGifts;
        $this->cart = $cart;
        $this->cartPresenter = $cartPresenter;
        $context = Context::getContext();
        $this->translator = $context->getTranslator();
        $this->link = $context->link;
        $this->imageRetriever = new ImageRetriever($this->link);
        $this->priceFormatter = new PriceFormatter();
        parent::__construct();
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getProducts(): array
    {
        if ($this->shouldSeparateGifts) {
            $rawProducts = $this->cart->getProductsWithSeparatedGifts();
        } else {
            $rawProducts = $this->cart->getProducts(true);
        }

        $products = array_map([$this, 'presentProduct'], $rawProducts);
        $this->products = $this->cartPresenter->addCustomizedData($products, $this->cart);

        return $this->products;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getTotals(): array
    {
        $total_excluding_tax = $this->cart->getOrderTotal(false);
        $total_including_tax = $this->cart->getOrderTotal(true);

        $this->totals = [
            'total' => [
                'type' => 'total',
                'label' => $this->translator->trans('Total', [], 'Shop.Theme.Checkout'),
                'amount' => $this->cartPresenter->includeTaxes() ? $total_including_tax : $total_excluding_tax,
                'value' => $this->priceFormatter->format(
                    $this->cartPresenter->includeTaxes() ? $total_including_tax : $total_excluding_tax
                ),
            ],
            'total_including_tax' => [
                'type' => 'total',
                'label' => $this->translator->trans('Total (tax incl.)', [], 'Shop.Theme.Checkout'),
                'amount' => $total_including_tax,
                'value' => $this->priceFormatter->format($total_including_tax),
            ],
            'total_excluding_tax' => [
                'type' => 'total',
                'label' => $this->translator->trans('Total (tax excl.)', [], 'Shop.Theme.Checkout'),
                'amount' => $total_excluding_tax,
                'value' => $this->priceFormatter->format($total_excluding_tax),
            ],
        ];

        return $this->totals;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getSubtotals(): array
    {
        $subtotals = [];
        $totalCartAmount = $this->cart->getOrderTotal($this->cartPresenter->includeTaxes(), Cart::ONLY_PRODUCTS);
        $total_discount = $this->cart->getDiscountSubtotalWithoutGifts($this->cartPresenter->includeTaxes());
        $subtotals['products'] = [
            'type' => 'products',
            'label' => $this->translator->trans('Subtotal', [], 'Shop.Theme.Checkout'),
            'amount' => $totalCartAmount,
            'value' => $this->priceFormatter->format($totalCartAmount),
        ];
        if ($total_discount) {
            $subtotals['discounts'] = [
                'type' => 'discount',
                'label' => $this->translator->trans('Discount(s)', [], 'Shop.Theme.Checkout'),
                'amount' => $total_discount,
                'value' => $this->priceFormatter->format($total_discount),
            ];
        } else {
            $subtotals['discounts'] = null;
        }
        if ($this->cart->gift) {
            $giftWrappingPrice = ($this->cart->getGiftWrappingPrice($this->cartPresenter->includeTaxes()) != 0)
                ? $this->cart->getGiftWrappingPrice($this->cartPresenter->includeTaxes())
                : 0;

            $subtotals['gift_wrapping'] = [
                'type' => 'gift_wrapping',
                'label' => $this->translator->trans('Gift wrapping', [], 'Shop.Theme.Checkout'),
                'amount' => $giftWrappingPrice,
                'value' => ($giftWrappingPrice > 0)
                    ? $this->priceFormatter->convertAndFormat($giftWrappingPrice)
                    : $this->translator->trans('Free', [], 'Shop.Theme.Checkout'),
            ];
        }
        if (!$this->cart->isVirtualCart()) {
            $shippingCost = $this->cart->getTotalShippingCost(null, $this->cartPresenter->includeTaxes());
        } else {
            $shippingCost = 0;
        }
        $subtotals['shipping'] = [
            'type' => 'shipping',
            'label' => $this->translator->trans('Shipping', [], 'Shop.Theme.Checkout'),
            'amount' => $shippingCost,
            'value' => $this->getShippingDisplayValue($this->cart, $shippingCost),
        ];
        $subtotals['tax'] = null;
        if (Configuration::get('PS_TAX_DISPLAY')) {
            $total_excluding_tax = $this->cart->getOrderTotal(false);
            $total_including_tax = $this->cart->getOrderTotal(true);
            $taxAmount = $total_including_tax - $total_excluding_tax;
            $subtotals['tax'] = [
                'type' => 'tax',
                'label' => ($this->cartPresenter->includeTaxes())
                    ? $this->translator->trans('Included taxes', [], 'Shop.Theme.Checkout')
                    : $this->translator->trans('Taxes', [], 'Shop.Theme.Checkout'),
                'amount' => $taxAmount,
                'value' => $this->priceFormatter->format($taxAmount),
            ];
        }

        $this->subTotals = $subtotals;

        return $this->subTotals;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getProductsCount(): int
    {
        // If product list is already available, no need to execute a new sql query
        if (isset($this->products)) {
            $this->productsCount = array_reduce($this->products, function ($count, $product) {
                return $count + $product['quantity'];
            }, 0);

            return $this->productsCount;
        }

        // Product list is not available, only query the nb of products
        $this->productsCount = (int) $this->cart->getNbProducts($this->cart->id);

        return $this->productsCount;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getSummaryString(): string
    {
        $productsCount = $this->getProductsCount();

        $this->summaryString = $productsCount === 1 ?
            $this->translator->trans('1 item', [], 'Shop.Theme.Checkout') :
            $this->translator->trans('%count% items', ['%count%' => $productsCount], 'Shop.Theme.Checkout');

        return $this->summaryString;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getLabels(): array
    {
        $this->labels = [
            'tax_short' => ($this->cartPresenter->includeTaxes())
                ? $this->translator->trans('(tax incl.)', [], 'Shop.Theme.Global')
                : $this->translator->trans('(tax excl.)', [], 'Shop.Theme.Global'),
            'tax_long' => ($this->cartPresenter->includeTaxes())
                ? $this->translator->trans('(tax included)', [], 'Shop.Theme.Global')
                : $this->translator->trans('(tax excluded)', [], 'Shop.Theme.Global'),
        ];

        return $this->labels;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getIdAddressDelivery(): ?int
    {
        $this->idAddressDelivery = $this->cart->id_address_delivery;

        return $this->idAddressDelivery;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getIdAddressInvoice(): ?int
    {
        $this->idAddressInvoice = $this->cart->id_address_invoice;

        return $this->idAddressInvoice;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getIsVirtual(): bool
    {
        $this->isVirtual = $this->cart->isVirtualCart();

        return $this->isVirtual;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getVouchers(): array
    {
        $this->vouchers = $this->getTemplateVarVouchers();

        return $this->vouchers;
    }

    /**
     * @arrayAccess
     * @isRewritable
     */
    public function getDiscounts(): array
    {
        $vouchers = $this->getVouchers();
        $cartRulesIds = array_flip(array_map(
            function ($voucher) {
                return $voucher['id_cart_rule'];
            },
            $vouchers['added']
        ));

        $discounts = $this->cart->getDiscounts();
        $cart = $this->cart;
        $discounts = array_filter($discounts, function ($discount) use ($cartRulesIds, $cart) {
            $voucherCustomerId = (int) $discount['id_customer'];
            $voucherIsRestrictedToASingleCustomer = ($voucherCustomerId !== 0);
            $voucherIsEmptyCode = empty($discount['code']);
            if ($voucherIsRestrictedToASingleCustomer && $cart->id_customer !== $voucherCustomerId && $voucherIsEmptyCode) {
                return false;
            }

            return !array_key_exists($discount['id_cart_rule'], $cartRulesIds);
        });

        $this->discounts = $discounts;

        return $this->discounts;
    }

    /**
     * @arrayAccess
     * @isRewritable
     *
     * @indexName "minimalPurchase"
     */
    public function getMinimalPurchase(): float
    {
        $minimalPurchase = $this->priceFormatter->convertAmount((float) Configuration::get('PS_PURCHASE_MINIMUM'));
        Hook::exec('overrideMinimalPurchasePrice', [
            'minimalPurchase' => &$minimalPurchase,
        ]);

        $this->minimalPurchase = $minimalPurchase;

        return $this->minimalPurchase;
    }

    /**
     * @arrayAccess
     * @isRewritable
     * @indexName "minimalPurchaseRequired"
     */
    public function getMinimalPurchaseRequired(): string
    {
        $minimalPurchase = $this->getMinimalPurchase();
        $productsTotalExcludingTax = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $this->minimalPurchaseRequired = ($productsTotalExcludingTax < $minimalPurchase) ?
            $this->translator->trans(
                'A minimum shopping cart total of %amount% (tax excl.) is required to validate your order. Current cart total is %total% (tax excl.).',
                [
                    '%amount%' => $this->priceFormatter->format($minimalPurchase),
                    '%total%' => $this->priceFormatter->format($productsTotalExcludingTax),
                ],
                'Shop.Theme.Checkout'
            ) :
            '';

        return $this->minimalPurchaseRequired;
    }

    /**
     * Accepts a cart object with the shipping cost amount and formats the shipping cost display value accordingly.
     * If the shipping cost is 0, then we must check if this is because of a free carrier and thus display 'Free' or
     * simply because the system was unable to determine shipping cost at this point and thus send an empty string to hide the shipping line.
     *
     * @param Cart $cart
     * @param float $shippingCost
     *
     * @return string
     */
    private function getShippingDisplayValue($cart, $shippingCost): string
    {
        $shippingDisplayValue = '';

        // if one of the applied cart rules have free shipping, then the shipping display value is 'Free'
        foreach ($this->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                return $this->translator->trans('Free', [], 'Shop.Theme.Checkout');
            }
        }

        if ($shippingCost != 0) {
            $shippingDisplayValue = $this->priceFormatter->format($shippingCost);
        } else {
            $defaultCountry = null;

            if (isset(Context::getContext()->cookie->id_country)) {
                $defaultCountry = new Country((int) Context::getContext()->cookie->id_country);
            }

            $deliveryOptionList = $this->cart->getDeliveryOptionList($defaultCountry);

            if (count($deliveryOptionList) > 0) {
                foreach ($deliveryOptionList as $option) {
                    foreach ($option as $currentCarrier) {
                        if (isset($currentCarrier['is_free']) && $currentCarrier['is_free'] > 0) {
                            $shippingDisplayValue = $this->translator->trans('Free', [], 'Shop.Theme.Checkout');
                            break 2;
                        }
                    }
                }
            }
        }

        return $shippingDisplayValue;
    }

    private function getTemplateVarVouchers(): array
    {
        $cartVouchers = $this->cart->getCartRules();
        $vouchers = [];

        $cartHasTax = null === $this->cart->id ? false : $this->cart->getAverageProductsTaxRate() * 100;
        $freeShippingAlreadySet = false;
        /** @var array{id_cart_rule:int, name: string, code: string, reduction_percent: float, reduction_currency: int, free_shipping: bool, reduction_tax: bool, reduction_amount:float, value_real:float|int|string, value_tax_exc:float|int|string} $cartVoucher */
        foreach ($cartVouchers as $cartVoucher) {
            $vouchers[$cartVoucher['id_cart_rule']]['id_cart_rule'] = $cartVoucher['id_cart_rule'];
            $vouchers[$cartVoucher['id_cart_rule']]['name'] = $cartVoucher['name'];
            $vouchers[$cartVoucher['id_cart_rule']]['code'] = $cartVoucher['code'];
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_percent'] = $cartVoucher['reduction_percent'];
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_currency'] = $cartVoucher['reduction_currency'];
            $vouchers[$cartVoucher['id_cart_rule']]['free_shipping'] = (bool) $cartVoucher['free_shipping'];

            // Voucher reduction depending of the cart tax rule
            // if $cartHasTax & voucher is tax excluded, set amount voucher to tax included
            if ($cartHasTax && $cartVoucher['reduction_tax'] == '0') {
                $cartVoucher['reduction_amount'] = $cartVoucher['reduction_amount'] * (1 + $cartHasTax / 100);
            }

            $vouchers[$cartVoucher['id_cart_rule']]['reduction_amount'] = $cartVoucher['reduction_amount'];

            if ($this->cartVoucherHasGiftProductReduction($cartVoucher)) {
                $cartVoucher['reduction_amount'] = $cartVoucher['value_real'];
            }

            $totalCartVoucherReduction = 0;

            if ($this->cartVoucherHasFreeShippingOnly($cartVoucher)) {
                $freeShippingOnly = true;
                if ($freeShippingAlreadySet) {
                    unset($vouchers[$cartVoucher['id_cart_rule']]);
                    continue;
                } else {
                    $freeShippingAlreadySet = true;
                }
            } else {
                $freeShippingOnly = false;
                $totalCartVoucherReduction = $this->cartPresenter->includeTaxes() ? $cartVoucher['value_real'] : $cartVoucher['value_tax_exc'];
            }

            // when a voucher has only a shipping reduction, the value displayed must be "Free Shipping"
            if ($freeShippingOnly) {
                $cartVoucher['reduction_formatted'] = $this->translator->trans(
                    'Free shipping',
                    [],
                    'Admin.Shipping.Feature'
                );
            } else {
                $cartVoucher['reduction_formatted'] = '-' . $this->priceFormatter->format($totalCartVoucherReduction);
            }
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_formatted'] = $cartVoucher['reduction_formatted'];
            $vouchers[$cartVoucher['id_cart_rule']]['delete_url'] = $this->link->getPageLink(
                'cart',
                null,
                null,
                [
                    'deleteDiscount' => $cartVoucher['id_cart_rule'],
                    'token' => Tools::getToken(false),
                ]
            );
        }

        return [
            'allowed' => (int) CartRule::isFeatureActive(),
            'added' => $vouchers,
        ];
    }

    private function cartVoucherHasGiftProductReduction(array $cartVoucher): bool
    {
        return !empty($cartVoucher['gift_product']);
    }

    private function cartVoucherHasFreeShippingOnly(array $cartVoucher): bool
    {
        return !$this->cartVoucherHasPercentReduction($cartVoucher)
            && !$this->cartVoucherHasAmountReduction($cartVoucher)
            && !$this->cartVoucherHasGiftProductReduction($cartVoucher);
    }

    private function cartVoucherHasPercentReduction(array $cartVoucher): bool
    {
        return isset($cartVoucher['reduction_percent'])
            && $cartVoucher['reduction_percent'] > 0
            && $cartVoucher['reduction_amount'] == '0.00';
    }

    private function cartVoucherHasAmountReduction(array $cartVoucher): bool
    {
        return isset($cartVoucher['reduction_amount']) && $cartVoucher['reduction_amount'] > 0;
    }

    /**
     * @param array $rawProduct
     *
     * @return ProductLazyArray|ProductListingLazyArray
     */
    private function presentProduct(array $rawProduct)
    {
        $assembledProduct = $this->cartPresenter->getProductAssembler()->assembleProduct($rawProduct);
        $rawProduct = array_merge($assembledProduct, $rawProduct);

        if (isset($rawProduct['attributes']) && is_string($rawProduct['attributes'])) {
            $rawProduct['attributes'] = $this->cartPresenter->getAttributesArrayFromString($rawProduct['attributes']);
        }
        $rawProduct['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['up_quantity_url'] = $this->link->getUpQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['down_quantity_url'] = $this->link->getDownQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['update_quantity_url'] = $this->link->getUpdateQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $resetFields = [
            'ecotax_rate',
            'specific_prices',
            'customizable',
            'online_only',
            'reduction',
            'reduction_without_tax',
            'new',
            'condition',
            'pack',
        ];
        foreach ($resetFields as $field) {
            if (!array_key_exists($field, $rawProduct)) {
                $rawProduct[$field] = '';
            }
        }

        $rawProduct['price'] = Tools::ps_round($rawProduct['price'], Context::getContext()->getComputingPrecision());
        $rawProduct['price_wt'] = Tools::ps_round($rawProduct['price_wt'], Context::getContext()->getComputingPrecision());

        if ($this->cartPresenter->includeTaxes()) {
            $rawProduct['price_amount'] = $rawProduct['price'] = $rawProduct['price_wt'];
            $rawProduct['unit_price'] = $rawProduct['unit_price_tax_included'];
        } else {
            $rawProduct['price_amount'] = $rawProduct['price_tax_exc'] = $rawProduct['price'];
            $rawProduct['unit_price'] = $rawProduct['unit_price_tax_excluded'];
        }

        $rawProduct['total'] = $this->priceFormatter->format(
            $this->cartPresenter->includeTaxes() ?
                $rawProduct['total_wt'] :
                $rawProduct['total']
        );

        $rawProduct['quantity_wanted'] = $rawProduct['cart_quantity'];

        $presenter = new ProductListingPresenter(
            $this->imageRetriever,
            $this->link,
            $this->priceFormatter,
            new ProductColorsRetriever(),
            $this->translator
        );

        return $presenter->present(
            $this->cartPresenter->getSettings(),
            $rawProduct,
            Context::getContext()->language
        );
    }
}
