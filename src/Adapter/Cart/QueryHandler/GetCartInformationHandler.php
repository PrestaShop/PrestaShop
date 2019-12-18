<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Address;
use AddressFormat;
use Carrier;
use Cart;
use CartRule;
use Currency;
use Customer;
use Language;
use Link;
use Message;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartDeliveryOption;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartShipping;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartSummary;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CustomizationFieldData;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\Customization;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShopException;
use Product;

/**
 * Handles GetCartInformation query using legacy object models
 */
final class GetCartInformationHandler extends AbstractCartHandler implements GetCartInformationHandlerInterface
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
     * @param LocaleInterface $locale
     * @param int $contextLangId
     * @param Link $contextLink
     */
    public function __construct(
        LocaleInterface $locale,
        int $contextLangId,
        Link $contextLink
    ) {
        $this->locale = $locale;
        $this->contextLangId = $contextLangId;
        $this->contextLink = $contextLink;
    }

    /**
     * @param GetCartInformation $query
     *
     * @return CartInformation
     *
     * @throws CartNotFoundException
     * @throws LocalizationException
     * @throws PrestaShopException
     */
    public function handle(GetCartInformation $query): CartInformation
    {
        $cart = $this->getCart($query->getCartId());
        $currency = new Currency($cart->id_currency);
        $language = new Language($cart->id_lang);

        $legacySummary = $cart->getSummaryDetails(null, true);
        $addresses = $this->getAddresses($cart);

        return new CartInformation(
            $cart->id,
            $this->extractProductsFromLegacySummary($cart, $legacySummary),
            (int) $currency->id,
            (int) $language->id,
            $this->extractCartRulesFromLegacySummary($legacySummary, $currency),
            $addresses,
            $this->extractSummaryFromLegacySummary($legacySummary, $currency, $cart),
            $addresses ? $this->extractShippingFromLegacySummary($cart, $legacySummary) : null
        );
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

        foreach ($customer->getAddresses($cart->id_lang) as $data) {
            $addressId = (int) $data['id_address'];
            $countryIsEnabled = (bool) Address::isCountryActiveById($addressId);

            // filter out disabled countries
            if (!$countryIsEnabled) {
                continue;
            }

            $cartAddresses[$addressId] = new CartAddress(
                $addressId,
                $data['alias'],
                AddressFormat::generateAddress(new Address($addressId), [], '<br />'),
                (int) $cart->id_address_delivery === $addressId,
                (int) $cart->id_address_invoice === $addressId
            );
        }

        return array_values($cartAddresses);
    }

    /**
     * @param array $legacySummary
     * @param Currency $currency
     *
     * @return CartInformation\CartRule[]
     *
     * @throws LocalizationException
     */
    private function extractCartRulesFromLegacySummary(array $legacySummary, Currency $currency): array
    {
        $cartRules = [];

        foreach ($legacySummary['discounts'] as $discount) {
            $cartRules[] = new CartInformation\CartRule(
                (int) $discount['id_cart_rule'],
                $discount['name'],
                $discount['description'],
                $this->locale->formatPrice($discount['value_real'], $currency->iso_code)
            );
        }

        return $cartRules;
    }

    /**
     * @param Cart $cart
     * @param array $legacySummary
     *
     * @return CartProduct[]
     */
    private function extractProductsFromLegacySummary(Cart $cart, array $legacySummary): array
    {
        $products = [];
        foreach ($legacySummary['products'] as $product) {
            $products[] = new CartProduct(
                (int) $product['id_product'],
                isset($product['id_product_attribute']) ? (int) $product['id_product_attribute'] : 0,
                $product['name'],
                isset($product['attributes_small']) ? $product['attributes_small'] : '',
                $product['reference'],
                $product['price'],
                (int) $product['quantity'],
                $product['total'],
                $this->contextLink->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default'),
                $this->getProductCustomizedData($cart, $product)
            );
        }

        return $products;
    }

    /**
     * @param Cart $cart
     * @param array $legacySummary
     *
     * @return CartShipping|null
     */
    private function extractShippingFromLegacySummary(Cart $cart, array $legacySummary): ?CartShipping
    {
        $deliveryOptionsByAddress = $cart->getDeliveryOptionList();
        $deliveryAddress = (int) $cart->id_address_delivery;

        //Check if there is any delivery options available for cart delivery address
        if (!array_key_exists($deliveryAddress, $deliveryOptionsByAddress)) {
            return null;
        }

        /** @var Carrier $carrier */
        $carrier = $legacySummary['carrier'];

        return new CartShipping(
            (string) $legacySummary['total_shipping'],
            $this->getFreeShippingValue($cart),
            $this->fetchCartDeliveryOptions($deliveryOptionsByAddress, $deliveryAddress),
            (int) $carrier->id ?: null
        );
    }

    private function getFreeShippingValue(Cart $cart): bool
    {
        $cartRules = $cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING);
        $freeShipping = false;

        foreach ($cartRules as $cartRule) {
            if ($cartRule['id_cart_rule'] == CartRule::getIdByCode(CartRule::BO_ORDER_CODE_PREFIX . (int) $cart->id)) {
                $freeShipping = true;

                break;
            }
        }

        return $freeShipping;
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
            $this->locale->formatPrice($legacySummary['total_tax'], $currency->iso_code),
            $this->locale->formatPrice($legacySummary['total_price'], $currency->iso_code),
            $this->locale->formatPrice($legacySummary['total_price_without_tax'], $currency->iso_code),
            $orderMessage,
            $this->contextLink->getPageLink(
                'order',
                false,
                (int) $cart->id_lang,
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
            $cart->id_lang,
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

        return new CartInformation\Customization($customizationId, $productCustomizedFieldsData);
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
}
