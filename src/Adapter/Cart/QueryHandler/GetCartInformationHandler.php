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
use Cart;
use Currency;
use Customer;
use Language;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartAddress;
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
     * @throws PrestaShopException
     * @throws LocalizationException
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
            $this->extractProdcutsFromLegacySummary($legacySummary),
            (int) $currency->id,
            (int) $language->id,
            $this->extractCartRulesFromLegacySummary($legacySummary, $currency),
            $this->getAddresses($cart),
            [],
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

    //@todo: extract products to some CartProduct[] DTO array...
    private function extractProdcutsFromLegacySummary(array $legacySummary): array
    {
        return $legacySummary['products'];
    }
}
