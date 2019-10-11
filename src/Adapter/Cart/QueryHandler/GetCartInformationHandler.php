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
use Currency;
use Customer;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartDeliveryOption;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartShipping;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use PrestaShopException;

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
     * @param RepositoryInterface $localeRepository
     * @param string $locale
     */
    public function __construct(
        RepositoryInterface $localeRepository,
        string $locale
    ) {
        $this->locale = $localeRepository->getLocale($locale);
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

        //@todo: implement empty arguments
        return new CartInformation(
            $cart->id,
            $this->extractProductsFromLegacySummary($legacySummary),
            (int) $currency->id,
            (int) $language->id,
            $this->extractCartRulesFromLegacySummary($legacySummary, $currency),
            $this->getAddresses($cart),
            $this->extractShippingFromLegacySummary($cart, $legacySummary),
            []
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
        $addresses = $customer->getAddresses($cart->id_lang);
        $cartAddresses = [];

        foreach ($addresses as &$data) {
            $isDelivery = (int) $cart->id_address_delivery === (int) $data['id_address'];
            $isInvoice = (int) $cart->id_address_invoice === (int) $data['id_address'];

            $cartAddresses[] = new CartAddress(
                (int) $data['id_address'],
                $data['alias'],
                AddressFormat::generateAddress(new Address($data['id_address']), [], '<br />'),
                $isDelivery,
                $isInvoice
            );
        }

        return $cartAddresses;
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
     * @param array $legacySummary
     *
     * @return CartProduct[]
     */
    private function extractProductsFromLegacySummary(array $legacySummary): array
    {
        $products = [];
        foreach ($legacySummary['products'] as $product) {
            $products[] = new CartProduct(
                (int) $product['id_product'],
                isset($product['id_product_attribute']) ? (int) $product['id_product_attribute'] : 0,
                (int) $product['id_customization'],
                $product['name'],
                isset($product['attributes_small']) ? $product['attributes_small'] : '',
                $product['reference'],
                $product['price'],
                (int) $product['quantity'],
                $product['total'],
                (new Link())->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default')
            );
        }

        return $products;
    }

    /**
     * @param array $legacySummary
     *
     * @return CartShipping|null
     */
    private function extractShippingFromLegacySummary(Cart $cart, array $legacySummary): ?CartShipping
    {
        $carrierIsDefined = isset($legacySummary['carrier']) &&
            $legacySummary['carrier'] instanceof Carrier &&
            (int) $legacySummary['carrier']->id;

        if (!$carrierIsDefined) {
            return null;
        }

        /** @var Carrier $carrier */
        $carrier = $legacySummary['carrier'];

        return new CartShipping(
            (int) $carrier->id,
            $carrier->name,
            $carrier->delay,
            (string) $legacySummary['total_shipping'],
            (bool) $legacySummary['free_ship'],
            $this->fetchCartDeliveryOptions($cart)
        );
    }

    /**
     * Get cart delivery options
     *
     * @param Cart $cart
     *
     * @return array
     */
    private function fetchCartDeliveryOptions(Cart $cart)
    {
        $deliveryOptions = [];
        //legacy multishipping feature required to use carrier list per one package.
        // One cart had multiple packages which could have been split by several delivery addresses
        foreach ($cart->getPackageList() as $packages) {
            //now there should always be only one package because multishipping feature is removed
            foreach ($packages as $package) {
                //so the list of carriers should be shared across whole cart for single delivery address
                foreach ($package['carrier_list'] as $carrierId) {
                    $carrierId = (int) $carrierId;
                    $carrier = new Carrier($carrierId);
                    // make sure no duplicate
                    $deliveryOptions[$carrierId] = new CartDeliveryOption(
                        $carrierId,
                        $carrier->name,
                        $carrier->delay[\Context::getContext()->employee->id_lang]
                    );
                }
            }
        }

        //make sure array is not associative
        return array_values($deliveryOptions);
    }
}
