<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Customization;
use Pack;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductBasicInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCategoriesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCustomizationOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler\GetProductSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractorException;
use Product;
use Tag;

/**
 * Handles the query GetEditableProduct using legacy ObjectModel
 */
class GetProductForEditingHandler extends AbstractProductHandler implements GetProductForEditingHandlerInterface
{
    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @var GetProductSuppliersHandlerInterface
     */
    private $getProductSuppliersHandler;

    /**
     * @param NumberExtractor $numberExtractor
     * @param GetProductSuppliersHandlerInterface $getProductSuppliersHandler
     */
    public function __construct(
        NumberExtractor $numberExtractor,
        GetProductSuppliersHandlerInterface $getProductSuppliersHandler
    ) {
        $this->numberExtractor = $numberExtractor;
        $this->getProductSuppliersHandler = $getProductSuppliersHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductForEditing $query): ProductForEditing
    {
        $product = $this->getProduct($query->getProductId());

        return new ProductForEditing(
            (int) $product->id,
            (bool) $product->active,
            $this->getCustomizationOptions($product),
            $this->getBasicInformation($product),
            $this->getCategoriesInformation($product),
            $this->getPricesInformation($product),
            $this->getOptions($product),
            $this->getShippingInformation($product),
            $this->getSupplierOptions($product)
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductBasicInformation
     */
    private function getBasicInformation(Product $product): ProductBasicInformation
    {
        return new ProductBasicInformation(
            $this->getProductType($product),
            $product->name,
            $product->description,
            $product->description_short
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductCategoriesInformation
     */
    private function getCategoriesInformation(Product $product): ProductCategoriesInformation
    {
        $categoryIds = array_map('intval', $product->getCategories());
        $defaultCategoryId = (int) $product->id_category_default;

        return new ProductCategoriesInformation($categoryIds, $defaultCategoryId);
    }

    /**
     * @param Product $product
     *
     * @return ProductPricesInformation
     */
    private function getPricesInformation(Product $product): ProductPricesInformation
    {
        return new ProductPricesInformation(
            $this->numberExtractor->extract($product, 'price'),
            $this->numberExtractor->extract($product, 'ecotax'),
            (int) $product->id_tax_rules_group,
            (bool) $product->on_sale,
            $this->numberExtractor->extract($product, 'wholesale_price'),
            $this->numberExtractor->extract($product, 'unit_price'),
            (string) $product->unity,
            $this->numberExtractor->extract($product, 'unit_price_ratio')
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductType
     *
     * @throws ProductConstraintException
     */
    private function getProductType(Product $product): ProductType
    {
        if ($product->is_virtual) {
            $productTypeValue = ProductType::TYPE_VIRTUAL;
        } elseif (Pack::isPack($product->id)) {
            $productTypeValue = ProductType::TYPE_PACK;
        } elseif (!empty($product->getAttributeCombinations())) {
            $productTypeValue = ProductType::TYPE_COMBINATION;
        } else {
            $productTypeValue = ProductType::TYPE_STANDARD;
        }

        return new ProductType($productTypeValue);
    }

    /**
     * @param Product $product
     *
     * @return ProductOptions
     */
    private function getOptions(Product $product): ProductOptions
    {
        return new ProductOptions(
            $product->visibility,
            (bool) $product->available_for_order,
            (bool) $product->online_only,
            (bool) $product->show_price,
            $this->getLocalizedTagsList((int) $product->id),
            $product->condition,
            $product->isbn,
            $product->upc,
            $product->ean13,
            $product->mpn,
            $product->reference
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductShippingInformation
     *
     * @throws NumberExtractorException
     */
    private function getShippingInformation(Product $product): ProductShippingInformation
    {
        $carrierReferences = array_map(function ($carrier) {
            return (int) $carrier['id_reference'];
        }, $product->getCarriers());

        return new ProductShippingInformation(
            $this->numberExtractor->extract($product, 'width'),
            $this->numberExtractor->extract($product, 'height'),
            $this->numberExtractor->extract($product, 'depth'),
            $this->numberExtractor->extract($product, 'weight'),
            $this->numberExtractor->extract($product, 'additional_shipping_cost'),
            $carrierReferences,
            (int) $product->additional_delivery_times,
            $product->delivery_in_stock,
            $product->delivery_out_stock
        );
    }

    /**
     * @param int $productId
     *
     * @return LocalizedTags[]
     */
    private function getLocalizedTagsList(int $productId): array
    {
        $tags = Tag::getProductTags($productId);

        if (!$tags) {
            return [];
        }

        $localizedTagsList = [];

        foreach ($tags as $langId => $localizedTags) {
            $localizedTagsList[] = new LocalizedTags((int) $langId, $localizedTags);
        }

        return $localizedTagsList;
    }

    /**
     * @param Product $product
     *
     * @return ProductCustomizationOptions
     */
    private function getCustomizationOptions(Product $product): ProductCustomizationOptions
    {
        if (!Customization::isFeatureActive()) {
            return ProductCustomizationOptions::createNotCustomizable();
        }

        $textFieldsCount = (int) $product->text_fields;
        $fileFieldsCount = (int) $product->uploadable_files;

        switch ((int) $product->customizable) {
            case ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION:
                return ProductCustomizationOptions::createAllowsCustomization($textFieldsCount, $fileFieldsCount);
                break;
            case ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION:
                return ProductCustomizationOptions::createRequiresCustomization($textFieldsCount, $fileFieldsCount);
                break;
            default:
                return ProductCustomizationOptions::createNotCustomizable();
        }
    }

    /**
     * @param Product $product
     *
     * @return ProductSupplierOptions
     */
    private function getSupplierOptions(Product $product): ProductSupplierOptions
    {
        return new ProductSupplierOptions(
            (int) $product->id_supplier,
            $this->getProductSuppliersHandler->handle(new GetProductSuppliers((int) $product->id))
        );
    }
}
