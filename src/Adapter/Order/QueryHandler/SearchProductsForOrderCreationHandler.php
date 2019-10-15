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

namespace PrestaShop\PrestaShop\Adapter\Order\QueryHandler;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\SearchProductsForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\SearchProductsForOrderCreationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ProductCustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ProductForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ProductCombinationForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ProductCombinationsForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\ProductsForOrderCreation;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use PrestaShopException;
use Product;

/**
 * Handles product for order creation search
 */
final class SearchProductsForOrderCreationHandler implements SearchProductsForOrderCreationHandlerInterface
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
    public function handle(SearchProductsForOrderCreation $query): ProductsForOrderCreation
    {
        $products = Product::searchByName($this->langId, $query->getPhrase());

        $productForOrderCreation = [];

        if ($products) {
            foreach ($products as $product) {
                $productForOrderCreation[] = $this->getProductForOrderCreation(new Product($product['id_product']));
            }
        }

        return new ProductsForOrderCreation($productForOrderCreation);
    }

    /**
     * @param Product $product
     *
     * @return ProductForOrderCreation
     *
     * @throws LocalizationException
     */
    private function getProductForOrderCreation(Product $product): ProductForOrderCreation
    {
        /** @var Locale $locale */
        $locale = $this->localeRepository->getLocale($this->locale);
        $priceTaxExcluded = Product::getPriceStatic($product->id, false);

        $customizationFields = is_array($product->getCustomizationFields()) ?
            $this->getProductCustomizationFields($product->getCustomizationFields()) :
            null
        ;

        $combinations = !empty($product->getAttributeCombinations()) ?
            $this->getProductCombinations($product->getAttributeCombinations(), $product->id) :
            null;

        $productForOrderCreation = new ProductForOrderCreation(
            $product->id,
            $product->name[$this->langId],
            $locale->formatPrice($priceTaxExcluded, $this->currencyCode),
            Product::getQuantity($product->id)
        );

        if (null !== $customizationFields) {
            $productForOrderCreation->setCustomizationFields($customizationFields);
        }

        if (null !== $combinations) {
            $productForOrderCreation->setCombinations($combinations);
        }

        return $productForOrderCreation;
    }

    /**
     * @param array $fields
     *
     * @return ProductCustomizationFields|null
     */
    private function getProductCustomizationFields(array $fields): ?ProductCustomizationFields
    {
        $fieldsForProductOrderCreations = [];

        foreach ($fields as $typeId => $typeFields) {
            foreach ($typeFields as $field) {
                $customizationField = new ProductCustomizationField(
                    (int) $field[$this->langId]['id_customization_field'],
                    (int) $typeId,
                    $field[$this->langId]['name'],
                    (bool) $field[$this->langId]['required']
                );

                $fieldsForProductOrderCreations[] = $customizationField;
            }
        }

        return new ProductCustomizationFields($fieldsForProductOrderCreations);
    }

    /**
     * @param array $combinations
     * @param int $productId
     *
     * @return ProductCombinationsForOrderCreation|null
     *
     * @throws LocalizationException
     */
    private function getProductCombinations(array $combinations, int $productId): ?ProductCombinationsForOrderCreation
    {
        $productCombinations = new ProductCombinationsForOrderCreation();

        /** @var Locale $locale */
        $locale = $this->localeRepository->getLocale($this->locale);

        foreach ($combinations as $combination) {
            $productAttributeId = (int) $combination['id_product_attribute'];
            if ($productCombination = $productCombinations->getCombination($productAttributeId)) {
                $productCombination->appendAttributeName($combination['attribute_name']);

                continue;
            }

            $priceTaxExcluded = Product::getPriceStatic($productId, false, $productAttributeId);

            $productCombination = new ProductCombinationForOrderCreation(
                $productAttributeId,
                $combination['attribute_name'],
                $combination['quantity'],
                $locale->formatPrice($priceTaxExcluded, $this->currencyCode)
            );

            $productCombinations->addCombination($productCombination);
        }

        return $productCombinations;
    }
}
