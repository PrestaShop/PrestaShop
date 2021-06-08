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

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Address;
use AddressFormat;
use Carrier;
use Cart;
use CartRule;
use Currency;
use Customer;
use Link;
use Message;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartForOrderCreationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartDeliveryOption;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartShipping;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartSummary;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\Customization;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CustomizationFieldData;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShopException;
use Product;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;

/**
 * Handles GetCartForOrderCreation query using legacy object models
 */
final class GetCartForOrderCreationHandler extends AbstractCartHandler implements GetCartForOrderCreationHandlerInterface
{
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var Link
     */
    private $contextLink;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param LocaleInterface $locale
     * @param int $contextLangId
     * @param Link $contextLink
     * @param ContextStateManager $contextStateManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        LocaleInterface $locale,
        int $contextLangId,
        Link $contextLink,
        ContextStateManager $contextStateManager,
        TranslatorInterface $translator
    ) {
        $this->locale = $locale;
        $this->contextLangId = $contextLangId;
        $this->contextLink = $contextLink;
        $this->contextStateManager = $contextStateManager;
        $this->translator = $translator;
    }

    /**
     * @param GetCartForOrderCreation $query
     *
     * @return CartForOrderCreation
     *
     * @throws CartNotFoundException
     * @throws LocalizationException
     * @throws PrestaShopException
     */
    public function handle(GetCartForOrderCreation $query): CartForOrderCreation
    {
        $cart = $this->getCart($query->getCartId());
        $currency = new Currency($cart->id_currency);
        $language = $cart->getAssociatedLanguage();

        $this->contextStateManager
            ->setCart($cart)
            ->setCurrency($currency)
            ->setLanguage($language)
            ->setCustomer(new Customer($cart->id_customer))
            ->setShop(new Shop($cart->id_shop))
        ;

        try {
            $addresses = $this->getAddresses($cart);

            if ($query->hideDiscounts()) {
                $legacySummary = $cart->getSummaryDetails($cart->getAssociatedLanguage()->getId(), true);
                $products = $this->extractProductsWithGiftSplitFromLegacySummary($cart, $legacySummary, $currency);
            } else {
                $legacySummary = $cart->getRawSummaryDetails($cart->getAssociatedLanguage()->getId(), true);
                $products = $this->extractProductsFromLegacySummary($cart, $legacySummary, $currency);
            }

            $result = new CartForOrderCreation(
                $cart->id,
                $products,
                (int) $currency->id,
                (int) $language->id,
                $this->extractCartRulesFromLegacySummary($cart, $legacySummary, $currency, $query->hideDiscounts()),
                $addresses,
                $this->extractSummaryFromLegacySummary($legacySummary, $currency, $cart),
                $addresses ? $this->extractShippingFromLegacySummary($cart, $legacySummary, $query->hideDiscounts()) : null
            );
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }

        return $result;
    }

    /**
     * @param Cart $cart
     *
     * @return CartAddress[]
     */
    private function getAddresses(Cart $cart): array
    {
        $customer = new Customer($cart->id_customer);
        $cartAddresses = [];

        foreach ($customer->getAddresses($cart->getAssociatedLanguage()->getId()) as $data) {
            $addressId = (int) $data['id_address'];
            $cartAddresses[$addressId] = $this->buildCartAddress($addressId, $cart);
        }

        // Add addresses already assigned to cart if absent (in case they are deleted)
        if (0 !== (int) $cart->id_address_delivery && !isset($cartAddresses[$cart->id_address_delivery])) {
            $cartAddresses[$cart->id_address_delivery] = $this->buildCartAddress(
                $cart->id_address_delivery,
                $cart
            );
        }
        if (0 !== (int) $cart->id_address_invoice && !isset($cartAddresses[$cart->id_address_invoice])) {
            $cartAddresses[$cart->id_address_invoice] = $this->buildCartAddress(
                $cart->id_address_invoice,
                $cart
            );
        }

        return array_values($cartAddresses);
    }

    /**
     * @param int $addressId
     * @param Cart $cart
     *
     * @return CartAddress
     */
    private function buildCartAddress(int $addressId, Cart $cart): CartAddress
    {
        $address = new Address($addressId);

        return new CartAddress(
            $address->id,
            $address->alias,
            AddressFormat::generateAddress($address, [], '<br />'),
            (int) $cart->id_address_delivery === $address->id,
            (int) $cart->id_address_invoice === $address->id
        );
    }

    /**
     * @param Cart $cart
     * @param array $legacySummary
     * @param Currency $currency
     * @param bool $hideDiscounts
     *
     * @return CartForOrderCreation\CartRule[]
     */
    private function extractCartRulesFromLegacySummary(Cart $cart, array $legacySummary, Currency $currency, bool $hideDiscounts = false): array
    {
        $cartRules = [];

        foreach ($legacySummary['discounts'] as $discount) {
            $cartRuleId = (int) $discount['id_cart_rule'];
            $cartRules[$cartRuleId] = new CartForOrderCreation\CartRule(
                (int) $discount['id_cart_rule'],
                $discount['name'],
                $discount['description'],
                (new DecimalNumber((string) $discount['value_tax_exc']))->round($currency->precision)
            );
        }

        if ($hideDiscounts) {
            foreach ($cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $giftRule) {
                $giftRuleId = (int) $giftRule['id_cart_rule'];
                $finalValue = new DecimalNumber((string) $giftRule['value_tax_exc']);

                if (isset($cartRules[$giftRuleId])) {
                    // it is possible that one cart rule can have a gift product, but also have other conditions,
                    //so we need to sum their reduction values
                    /** @var CartForOrderCreation\CartRule $cartRule */
                    $cartRule = $cartRules[$giftRuleId];
                    $finalValue = $finalValue->plus(new DecimalNumber($cartRule->getValue()));
                }

                $cartRules[$giftRuleId] = new CartForOrderCreation\CartRule(
                    (int) $giftRule['id_cart_rule'],
                    $giftRule['name'],
                    $giftRule['description'],
                    $finalValue->round($currency->precision)
                );
            }
        }

        return $cartRules;
    }

    /**
     * @param Cart $cart
     * @param array $legacySummary
     * @param Currency $currency
     *
     * @return CartProduct[]
     */
    private function extractProductsWithGiftSplitFromLegacySummary(Cart $cart, array $legacySummary, Currency $currency): array
    {
        $products = [];
        $mergedGifts = $this->mergeGiftProducts($legacySummary['gift_products']);

        foreach ($legacySummary['products'] as $product) {
            $productKey = $this->generateUniqueProductKey($product);

            //decrease product quantity for each identical product which is marked as gift
            if (isset($mergedGifts[$productKey])) {
                $identicalGiftedProduct = $mergedGifts[$productKey];
                $product['quantity'] -= $identicalGiftedProduct['quantity'];
            }

            $products[] = $this->buildCartProduct($cart, $currency, $product);
        }

        foreach ($mergedGifts as $product) {
            $products[] = $this->buildCartProduct($cart, $currency, $product);
        }

        return $products;
    }

    /**
     * @param Cart $cart
     * @param array $legacySummary
     * @param Currency $currency
     *
     * @return CartProduct[]
     */
    private function extractProductsFromLegacySummary(Cart $cart, array $legacySummary, Currency $currency): array
    {
        $products = [];
        foreach ($legacySummary['products'] as $product) {
            $products[] = $this->buildCartProduct($cart, $currency, $product);
        }

        return $products;
    }

    /**
     * @param array $giftProducts
     *
     * @return array
     */
    private function mergeGiftProducts(array $giftProducts): array
    {
        $mergedGifts = [];

        foreach ($giftProducts as $giftProduct) {
            $productKey = $this->generateUniqueProductKey($giftProduct);

            if (!isset($mergedGifts[$productKey])) {
                // set first gift and make sure its quantity is 1.
                $mergedGifts[$productKey] = $giftProduct;
                $mergedGifts[$productKey]['quantity'] = 1;
            } else {
                //increase existing gift quantity by 1
                ++$mergedGifts[$productKey]['quantity'];
            }
        }

        return $mergedGifts;
    }

    /**
     * Forms a unique product key using combination and customization ids.
     *
     * @param array $product
     *
     * @return string
     */
    private function generateUniqueProductKey(array $product): string
    {
        return sprintf(
            '%s_%s_%s',
            (int) $product['id_product'],
            (int) $product['id_product_attribute'],
            (int) $product['id_customization']
        );
    }

    /**
     * @param Cart $cart
     * @param array $legacySummary
     * @param bool $hideDiscounts
     *
     * @return CartShipping|null
     */
    private function extractShippingFromLegacySummary(Cart $cart, array $legacySummary, bool $hideDiscounts = true): ?CartShipping
    {
        $deliveryOptionsByAddress = $cart->getDeliveryOptionList();
        $deliveryAddress = (int) $cart->id_address_delivery;

        //Check if there is any delivery options available for cart delivery address
        if (!array_key_exists($deliveryAddress, $deliveryOptionsByAddress)) {
            return null;
        }

        /** @var Carrier $carrier */
        $carrier = $legacySummary['carrier'];
        $isFreeShipping = !empty($cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING));

        return new CartShipping(
            $isFreeShipping && $hideDiscounts ? '0' : (string) $legacySummary['total_shipping'],
            $isFreeShipping,
            $this->fetchCartDeliveryOptions($deliveryOptionsByAddress, $deliveryAddress),
            (int) $carrier->id ?: null,
            (bool) $cart->gift,
            (bool) $cart->recyclable,
            $cart->gift_message
        );
    }

    /**
     * Fetch CartDeliveryOption[] DTO's from legacy array
     *
     * @param array $deliveryOptionsByAddress
     * @param int $deliveryAddressId
     *
     * @return array
     */
    private function fetchCartDeliveryOptions(array $deliveryOptionsByAddress, int $deliveryAddressId)
    {
        $deliveryOptions = [];
        // legacy multishipping feature allowed to split cart shipping to multiple addresses.
        // now when the multishipping feature is removed
        // the list of carriers should be shared across whole cart for single delivery address
        foreach ($deliveryOptionsByAddress[$deliveryAddressId] as $deliveryOption) {
            foreach ($deliveryOption['carrier_list'] as $carrier) {
                $carrier = $carrier['instance'];
                // make sure there is no duplicate carrier
                $deliveryOptions[(int) $carrier->id] = new CartDeliveryOption(
                    (int) $carrier->id,
                    $carrier->name,
                    $carrier->delay[$this->contextLangId]
                );
            }
        }

        //make sure array is not associative
        return array_values($deliveryOptions);
    }

    /**
     * @param array $legacySummary
     * @param Currency $currency
     * @param Cart $cart
     *
     * @return CartSummary
     *
     * @throws LocalizationException
     */
    private function extractSummaryFromLegacySummary(array $legacySummary, Currency $currency, Cart $cart): CartSummary
    {
        $cartId = (int) $cart->id;

        $discount = $this->locale->formatPrice(-1 * $legacySummary['total_discounts_tax_exc'], $currency->iso_code);

        $orderMessage = '';
        if ($message = Message::getMessageByCartId($cartId)) {
            $orderMessage = $message['message'];
        }

        return new CartSummary(
            $this->locale->formatPrice($legacySummary['total_products'], $currency->iso_code),
            $discount,
            $this->locale->formatPrice($legacySummary['total_shipping'], $currency->iso_code),
            $this->locale->formatPrice($legacySummary['total_shipping_tax_exc'], $currency->iso_code),
            $this->locale->formatPrice($legacySummary['total_tax'], $currency->iso_code),
            $this->locale->formatPrice($legacySummary['total_price'], $currency->iso_code),
            $this->locale->formatPrice($legacySummary['total_price_without_tax'], $currency->iso_code),
            $orderMessage,
            $this->contextLink->getPageLink(
                'order',
                false,
                (int) $cart->getAssociatedLanguage()->getId(),
                http_build_query([
                    'step' => 3,
                    'recover_cart' => $cartId,
                    'token_cart' => md5(_COOKIE_KEY_ . 'recover_cart_' . $cartId),
                ])
            )
        );
    }

    /**
     * Provides product customizations data
     *
     * @param Cart $cart
     * @param array $product the product array from legacy summary
     *
     * @return Customization|null
     */
    private function getProductCustomizedData(Cart $cart, array $product): ?Customization
    {
        $customizationId = (int) $product['id_customization'];

        if (!$customizationId) {
            return null;
        }

        $customizations = Product::getAllCustomizedDatas(
            $cart->id,
            $cart->getAssociatedLanguage()->getId(),
            true,
            null,
            $customizationId
        );

        if ($customizations) {
            $productCustomizedFieldsData = $this->getProductCustomizedFieldsData($customizations, $product);
        }

        if (empty($productCustomizedFieldsData)) {
            return null;
        }

        return new CartForOrderCreation\Customization($customizationId, $productCustomizedFieldsData);
    }

    /**
     * Provides customized fields data for product
     *
     * @param array $customizations
     * @param array $product
     *
     * @return array
     */
    private function getProductCustomizedFieldsData(array $customizations, array $product)
    {
        $customizationFieldsData = [];

        if (isset($customizations[$product['id_product']][$product['id_product_attribute']])) {
            foreach ($customizations[$product['id_product']][$product['id_product_attribute']] as $customizationByAddress) {
                foreach ($customizationByAddress as $customization) {
                    if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                        foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                            $customizationFieldsData[] = new CustomizationFieldData(
                                Product::CUSTOMIZE_TEXTFIELD,
                                $text['name'],
                                $text['value']
                            );
                        }
                    }

                    if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                        foreach ($customization['datas'][Product::CUSTOMIZE_FILE] as $file) {
                            $customizationFieldsData[] = new CustomizationFieldData(
                                Product::CUSTOMIZE_FILE,
                                $file['name'],
                                _THEME_PROD_PIC_DIR_ . $file['value'] . '_small'
                            );
                        }
                    }
                }
            }
        }

        return $customizationFieldsData;
    }

    /**
     * @param Cart $cart
     * @param Currency $currency
     * @param array $product
     *
     * @return CartProduct
     */
    private function buildCartProduct(
        Cart $cart,
        Currency $currency,
        array $product
    ): CartProduct {
        return new CartProduct(
            (int) $product['id_product'],
            isset($product['id_product_attribute']) ? (int) $product['id_product_attribute'] : 0,
            $product['name'],
            isset($product['attributes_small']) ? $product['attributes_small'] : '',
            $product['reference'],
            Tools::ps_round($product['price'], $currency->precision),
            $product['quantity'],
            Tools::ps_round($product['total'], $currency->precision),
            $this->contextLink->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default'),
            $this->getProductCustomizedData($cart, $product),
            Product::getQuantity(
                (int) $product['id_product'],
                isset($product['id_product_attribute']) ? (int) $product['id_product_attribute'] : null
            ),
            Product::isAvailableWhenOutOfStock((int) $product['out_of_stock']) !== 0,
            !empty($product['is_gift'])
        );
    }
}
