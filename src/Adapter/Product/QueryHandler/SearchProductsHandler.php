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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Context;
use Currency;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCombination;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCustomizationField;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use Product;

/**
 * Handles products search using legacy object model
 */
final class SearchProductsHandler implements SearchProductsHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var LocaleInterface
     */
    private $contextLocale;

    /*
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param int $contextLangId
     * @param LocaleInterface $contextLocale
     */
    public function __construct(int $contextLangId, LocaleInterface $contextLocale, ContextStateManager $contextStateManager)
    {
        $this->contextLangId = $contextLangId;
        $this->contextLocale = $contextLocale;
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     *
     * @param SearchProducts $query
     *
     * @return array
     */
    public function handle(SearchProducts $query): array
    {
        $currencyId = Currency::getIdByIsoCode($query->getAlphaIsoCode());
        $this->contextStateManager->setCurrency(new Currency($currencyId));

        $products = Product::searchByName(
            $this->contextLangId,
            $query->getPhrase(),
            null,
            $query->getResultsLimit()
        );

        $foundProducts = [];

        if ($products) {
            foreach ($products as $product) {
                $foundProduct = $this->createFoundProductFromLegacy(new Product($product['id_product']), $query);
                $foundProducts[$foundProduct->getProductId()] = $foundProduct;
            }
        }

        $this->contextStateManager->restoreContext();

        return $foundProducts;
    }

    /**
     * @param Product $product
     * @param SearchProducts $query
     *
     * @return FoundProduct
     */
    private function createFoundProductFromLegacy(Product $product, SearchProducts $query): FoundProduct
    {
        //@todo: sort products alphabetically

        $priceTaxExcluded = Product::getPriceStatic($product->id, false);
        $priceTaxIncluded = Product::getPriceStatic($product->id, true);

        $foundProduct = new FoundProduct(
            $product->id,
            $product->name[$this->contextLangId],
            $this->contextLocale->formatPrice($priceTaxExcluded, $query->getCurrency()->iso_code),
            $priceTaxIncluded,
            $priceTaxExcluded,
            Product::getQuantity($product->id),
            $this->getProductCombinations($product, $query->getCurrency()->iso_code),
            $this->getProductCustomizationFields($product)
        );

        return $foundProduct;
    }

    /**
     * @param Product $product
     *
     * @return ProductCustomizationField[]
     */
    private function getProductCustomizationFields(Product $product): array
    {
        $fields = $product->getCustomizationFields();
        $customizationFields = [];

        if (false !== $fields) {
            foreach ($fields as $typeId => $typeFields) {
                foreach ($typeFields as $field) {
                    $customizationField = new ProductCustomizationField(
                        (int) $field[$this->contextLangId]['id_customization_field'],
                        (int) $typeId,
                        $field[$this->contextLangId]['name'],
                        (bool) $field[$this->contextLangId]['required']
                    );

                    $customizationFields[$customizationField->getCustomizationFieldId()] = $customizationField;
                }
            }
        }

        return $customizationFields;
    }

    /**
     * @param Product $product
     * @param string $currencyCode
     *
     * @return ProductCombination[]
     */
    private function getProductCombinations(Product $product, $currencyIsoCode): array
    {
        $productCombinations = [];
        $combinations = $product->getAttributeCombinations();

        if (false !== $combinations) {
            foreach ($combinations as $combination) {
                $productAttributeId = (int) $combination['id_product_attribute'];
                $attribute = $combination['attribute_name'];

                if (isset($productCombinations[$productAttributeId])) {
                    $existingAttribute = $productCombinations[$productAttributeId]->getAttribute();
                    $attribute = $existingAttribute . ' - ' . $attribute;
                }

                $priceTaxExcluded = Product::getPriceStatic((int) $product->id, false, $productAttributeId);
                $priceTaxIncluded = Product::getPriceStatic((int) $product->id, true, $productAttributeId);

                $productCombination = new ProductCombination(
                    $productAttributeId,
                    $attribute,
                    $combination['quantity'],
                    $this->contextLocale->formatPrice($priceTaxExcluded, $currencyIsoCode),
                    $priceTaxExcluded,
                    $priceTaxIncluded
                );

                $productCombinations[$productCombination->getAttributeCombinationId()] = $productCombination;
            }
        }

        return $productCombinations;
    }
}
