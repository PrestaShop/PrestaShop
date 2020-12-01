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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Customization;
use DateTime;
use Pack;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
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
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductSeoOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductStockInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractorException;
use Product;
use Tag;

/**
 * Handles the query GetEditableProduct using legacy ObjectModel
 */
final class GetProductForEditingHandler extends AbstractProductHandler implements GetProductForEditingHandlerInterface
{
    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @param NumberExtractor $numberExtractor
     * @param StockAvailableRepository $stockAvailableRepository
     */
    public function __construct(
        NumberExtractor $numberExtractor,
        StockAvailableRepository $stockAvailableRepository
    ) {
        $this->numberExtractor = $numberExtractor;
        $this->stockAvailableRepository = $stockAvailableRepository;
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
            $this->getSeoOptions($product),
            $product->getAssociatedAttachmentIds(),
            $this->getProductStockInformation($product)
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
        } elseif ($product->hasCombinations()) {
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
            $product->reference,
            (int) $product->id_manufacturer
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
        $carrierReferences = array_map(function ($carrier): int {
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
     * @return ProductSeoOptions
     */
    private function getSeoOptions(Product $product): ProductSeoOptions
    {
        return new ProductSeoOptions(
            $product->meta_title,
            $product->meta_description,
            $product->link_rewrite,
            $product->redirect_type,
            (int) $product->id_type_redirected
        );
    }

    /**
     * Returns the product stock infos, it's important that the Product is fetched with stock data
     *
     * @param Product $product
     *
     * @return ProductStockInformation
     */
    private function getProductStockInformation(Product $product): ProductStockInformation
    {
        //@todo: In theory StockAvailable is created for each product when Product::add is called,
        //  but we should explore some multishop edgecases
        //  (like shop ids might be missing and foreach loop won't start resulting in a missing StockAvailable for product)
        $stockAvailable = $this->stockAvailableRepository->getForProduct(new ProductId($product->id));

        return new ProductStockInformation(
            (bool) $product->advanced_stock_management,
            (bool) $stockAvailable->depends_on_stock,
            (int) $product->pack_stock_type,
            (int) $stockAvailable->out_of_stock,
            (int) $stockAvailable->quantity,
            (int) $product->minimal_quantity,
            $stockAvailable->location,
            (int) $product->low_stock_threshold,
            (bool) $product->low_stock_alert,
            $product->available_now,
            $product->available_later,
            new DateTime($product->available_date)
        );
    }
}
