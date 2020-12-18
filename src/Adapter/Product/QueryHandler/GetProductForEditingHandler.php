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
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductDownloadRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductBasicInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCategoriesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCustomizationOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductSeoOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductStockInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
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
     * @var ProductDownloadRepository
     */
    private $productDownloadRepository;

    /**
     * @param NumberExtractor $numberExtractor
     * @param StockAvailableRepository $stockAvailableRepository
     * @param ProductDownloadRepository $productDownloadRepository
     */
    public function __construct(
        NumberExtractor $numberExtractor,
        StockAvailableRepository $stockAvailableRepository,
        ProductDownloadRepository $productDownloadRepository
    ) {
        $this->numberExtractor = $numberExtractor;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->productDownloadRepository = $productDownloadRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductForEditing $query): ProductForEditing
    {
        $product = $this->getProduct($query->getProductId());

        return new ProductForEditing(
            (int) $product->id,
            $this->getCustomizationOptions($product),
            $this->getBasicInformation($product),
            $this->getCategoriesInformation($product),
            $this->getPricesInformation($product),
            $this->getOptions($product),
            $this->getDetails($product),
            $this->getShippingInformation($product),
            $this->getSeoOptions($product),
            $product->getAssociatedAttachmentIds(),
            $this->getProductStockInformation($product),
            $this->getVirtualProductFile($product)
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
            $product->description_short,
            $this->getLocalizedTagsList((int) $product->id)
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
            (bool) $product->active,
            $product->visibility,
            (bool) $product->available_for_order,
            (bool) $product->online_only,
            (bool) $product->show_price,
            $product->condition,
            (bool) $product->show_condition,
            (int) $product->id_manufacturer
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductDetails
     */
    private function getDetails(Product $product): ProductDetails
    {
        return new ProductDetails(
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
                $options = ProductCustomizationOptions::createAllowsCustomization($textFieldsCount, $fileFieldsCount);
                break;
            case ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION:
                $options = ProductCustomizationOptions::createRequiresCustomization($textFieldsCount, $fileFieldsCount);
                break;
            default:
                $options = ProductCustomizationOptions::createNotCustomizable();
        }

        return $options;
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

    /**
     * Get virtual product file
     * legacy ProductDownload is referred as VirtualProductFile in Core
     *
     * @param Product $product
     *
     * @return VirtualProductFileForEditing|null
     *
     * @throws VirtualProductFileNotFoundException
     */
    private function getVirtualProductFile(Product $product): ?VirtualProductFileForEditing
    {
        $productDownload = $this->productDownloadRepository->findByProductId(new ProductId($product->id));

        if (!$productDownload) {
            return null;
        }

        return new VirtualProductFileForEditing(
            (int) $productDownload->id,
            $productDownload->filename,
            $productDownload->display_filename,
            (int) $productDownload->nb_days_accessible,
            (int) $productDownload->nb_downloadable,
            $productDownload->date_expiration === DateTimeUtil::NULL_VALUE ? null : new DateTime($productDownload->date_expiration)
        );
    }
}
