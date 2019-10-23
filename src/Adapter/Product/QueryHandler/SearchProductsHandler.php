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

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCombination;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCustomizationField;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use PrestaShopException;
use Product;

/**
 * Handles products search using legacy object model
 */
final class SearchProductsHandler implements SearchProductsHandlerInterface
{
    /**
     * @var int
     */
    private $langId;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param LegacyContext $context
     * @param RepositoryInterface $localeRepository
     * @param string $locale
     */
    public function __construct(LegacyContext $context, RepositoryInterface $localeRepository, string $locale)
    {
        $this->langId = $context->getLanguage()->getId();
        $this->currencyCode = $context->getContext()->currency->iso_code;
        $this->localeRepository = $localeRepository;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LocalizationException
     * @throws PrestaShopException
     */
    public function handle(SearchProducts $query): array
    {
        $products = Product::searchByName($this->langId, $query->getPhrase(), null, $query->getResultsLimit());

        $foundProducts = [];

        if ($products) {
            foreach ($products as $product) {
                $foundProduct = $this->createFoundProductFromLegacy(new Product($product['id_product']));
                $foundProducts[$foundProduct->getProductId()] = $foundProduct;
            }
        }

        return $foundProducts;
    }

    /**
     * @param Product $product
     *
     * @return FoundProduct
     *
     * @throws LocalizationException
     */
    private function createFoundProductFromLegacy(Product $product): FoundProduct
    {
        /** @var Locale $locale */
        $locale = $this->localeRepository->getLocale($this->locale);
        $priceTaxExcluded = Product::getPriceStatic($product->id, false);

        $foundProduct = new FoundProduct(
            $product->id,
            $product->name[$this->langId],
            $locale->formatPrice($priceTaxExcluded, $this->currencyCode),
            Product::getQuantity($product->id),
            $this->getProductCombinations($product),
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
                        (int) $field[$this->langId]['id_customization_field'],
                        (int) $typeId,
                        $field[$this->langId]['name'],
                        (bool) $field[$this->langId]['required']
                    );

                    $customizationFields[$customizationField->getCustomizationFieldId()] = $customizationField;
                }
            }
        }

        return $customizationFields;
    }

    /**
     * @param Product $product
     *
     * @return ProductCombination[]
     *
     * @throws LocalizationException
     */
    private function getProductCombinations(Product $product): array
    {
        $productCombinations = [];
        $combinations = $product->getAttributeCombinations();

        if (false !== $combinations) {
            /** @var Locale $locale */
            $locale = $this->localeRepository->getLocale($this->locale);

            foreach ($combinations as $combination) {
                $productAttributeId = (int) $combination['id_product_attribute'];
                $attribute = $combination['attribute_name'];

                if (isset($productCombinations[$productAttributeId])) {
                    $existingAttribute = $productCombinations[$productAttributeId]->getAttribute();
                    $attribute = $existingAttribute . ' - ' . $attribute;
                }

                $priceTaxExcluded = Product::getPriceStatic((int) $product->id, false, $productAttributeId);

                $productCombination = new ProductCombination(
                    $productAttributeId,
                    $attribute,
                    $combination['quantity'],
                    $locale->formatPrice($priceTaxExcluded, $this->currencyCode)
                );

                $productCombinations[$productCombination->getAttributeCombinationId()] = $productCombination;
            }
        }

        return $productCombinations;
    }
}
