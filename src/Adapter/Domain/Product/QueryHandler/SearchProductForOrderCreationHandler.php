<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Domain\Product\QueryHandler;

use Address;
use Configuration;
use Context;
use Currency;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsForOrderCreationHandlerInterface;
use Product;
use StockAvailable;
use Tools;
use Warehouse;

/**
 * Searches products for order creation using legacy object model
 */
final class SearchProductForOrderCreationHandler implements SearchProductsForOrderCreationHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(SearchProductsForOrderCreation $query)
    {
        $products = Product::searchByName(
            $query->getLanguageId()->getValue(),
            pSQL($query->getSearchQuery())
        );

        if (empty($products)) {
            return [];
        }

        $shopId = (int) Context::getContext()->shop->id;
        $languageId = $query->getLanguageId();
        $currency = new Currency($query->getCurrencyId()->getValue());

        foreach ($products as &$product) {
            $productId = (int) $products['id_product'];

            // Formatted price
            $product['formatted_price'] = Tools::displayPrice(Tools::convertPrice($product['price_tax_incl'], $currency), $currency);
            // Concret price
            $product['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_incl'], $currency), 2);
            $product['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_excl'], $currency), 2);
            $productObj = new Product($productId, false, $languageId->getValue());
            $combinations = [];
            $attributes = $productObj->getAttributesGroups($languageId->getValue());

            // Tax rate for this customer
            if (Tools::isSubmit('id_address')) {
                $product['tax_rate'] = $productObj->getTaxesRate(new Address(Tools::getValue('id_address')));
            }

            $product['warehouse_list'] = [];

            $isAdvancedStockManagementEnabled = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');

            foreach ($attributes as $attribute) {
                if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                    $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                }

                $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'] . ' - ';
                $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];

                if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                    $price_tax_incl = Product::getPriceStatic($productId, true, $attribute['id_product_attribute']);
                    $price_tax_excl = Product::getPriceStatic($productId, false, $attribute['id_product_attribute']);

                    $combinations[$attribute['id_product_attribute']]['price_tax_incl'] =
                        Tools::ps_round(Tools::convertPrice($price_tax_incl, $currency), 2);
                    $combinations[$attribute['id_product_attribute']]['price_tax_excl'] =
                        Tools::ps_round(Tools::convertPrice($price_tax_excl, $currency), 2);
                    $combinations[$attribute['id_product_attribute']]['formatted_price'] =
                        Tools::displayPrice(Tools::convertPrice($price_tax_excl, $currency), $currency);
                }

                if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
                    $combinations[$attribute['id_product_attribute']]['qty_in_stock'] =
                        StockAvailable::getQuantityAvailableByProduct(
                            $productId,
                            $attribute['id_product_attribute'],
                            $shopId
                        )
                    ;
                }

                if ($isAdvancedStockManagementEnabled && $product['advanced_stock_management']) {
                    $product['warehouse_list'][$attribute['id_product_attribute']] =
                        Warehouse::getProductWarehouseList($productId, $attribute['id_product_attribute']);
                } else {
                    $product['warehouse_list'][$attribute['id_product_attribute']] = [];
                }

                $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity(
                    $productId,
                    $attribute['id_product_attribute']
                );
            }

            if ($isAdvancedStockManagementEnabled && $product['advanced_stock_management']) {
                $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($productId);
            } else {
                $product['warehouse_list'][0] = [];
            }

            $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct($productId, 0, $shopId);

            foreach ($combinations as &$combination) {
                $combination['attributes'] = rtrim($combination['attributes'], ' - ');
            }
            $product['combinations'] = $combinations;

            if ($product['customizable']) {
                $productInstance = new Product($productId);
                $product['customization_fields'] = $productInstance->getCustomizationFields($products);
            }
        }

        return $products;
    }
}
