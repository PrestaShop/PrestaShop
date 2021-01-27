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

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
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
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\ProductFormDataProvider;

class ProductFormDataProviderTest extends TestCase
{
    private const PRODUCT_ID = 42;
    private const DEFAULT_CATEGORY_ID = 51;
    private const DEFAULT_VIRTUAL_PRODUCT_FILE_ID = 69;
    private const DEFAULT_QUANTITY = 12;

    public function testGetDefaultData()
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = new ProductFormDataProvider($queryBusMock);

        $expectedDefaultData = [
            'basic' => [
                'type' => ProductType::TYPE_STANDARD,
            ],
            'price' => [
                'price_tax_excluded' => 0,
                'price_tax_included' => 0,
                'wholesale_price' => 0,
                'unit_price' => 0,
            ],
            'shipping' => [
                'width' => 0,
                'height' => 0,
                'depth' => 0,
                'weight' => 0,
            ],
        ];

        $defaultData = $provider->getDefaultData();
        $this->assertEquals($expectedDefaultData, $defaultData);
    }

    /**
     * @dataProvider getExpectedData
     *
     * @param array $productData
     * @param array $expectedData
     */
    public function testGetData(array $productData, array $expectedData)
    {
        $queryBusMock = $this->createQueryBusMock($productData);
        $provider = new ProductFormDataProvider($queryBusMock);

        $formData = $provider->getData(static::PRODUCT_ID);
        $this->assertEquals($expectedData, $formData);
    }

    public function getExpectedData()
    {
        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [];
        yield [
            $productData,
            $expectedOutputData,
        ];

        $expectedOutputData = $this->getDefaultOutputData();
        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];
        $productData = [
            'name' => $localizedValues,
            'type' => ProductType::TYPE_COMBINATION,
            'description' => $localizedValues,
            'description_short' => $localizedValues,
        ];
        $expectedOutputData['basic']['name'] = $localizedValues;
        $expectedOutputData['basic']['type'] = ProductType::TYPE_COMBINATION;
        $expectedOutputData['basic']['description'] = $localizedValues;
        $expectedOutputData['basic']['description_short'] = $localizedValues;
        yield [
            $productData,
            $expectedOutputData,
        ];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'meta_title' => $localizedValues,
            'meta_description' => $localizedValues,
            'link_rewrite' => $localizedValues,
        ];
        $expectedOutputData['seo']['meta_title'] = $localizedValues;
        $expectedOutputData['seo']['meta_description'] = $localizedValues;
        $expectedOutputData['seo']['link_rewrite'] = $localizedValues;
        yield [
            $productData,
            $expectedOutputData,
        ];
    }

    /**
     * @param array $product
     *
     * @return ProductForEditing
     */
    private function createProductForEditing(array $product): ProductForEditing
    {
        return new ProductForEditing(
            static::PRODUCT_ID,
            ProductCustomizationOptions::createNotCustomizable(),
            $this->createBasic($product),
            $this->createCategories($product),
            $this->createPricesInformation($product),
            $this->createOptions($product),
            $this->createDetails($product),
            $this->createShippingInformation($product),
            $this->createSeoOptions($product),
            $product['attachments'] ?? [],
            $this->createProductStockInformation($product),
            $this->createVirtualProductFile($product)
        );
    }

    /**
     * @param array $product
     *
     * @return VirtualProductFileForEditing|null
     */
    private function createVirtualProductFile(array $product): ?VirtualProductFileForEditing
    {
        if (!isset($product['virtual_product_file'])) {
            return null;
        }

        return new VirtualProductFileForEditing(
            self::DEFAULT_VIRTUAL_PRODUCT_FILE_ID,
            $product['virtual_product_file']['filename'] ?? 'filename',
            $product['virtual_product_file']['display_filename'] ?? 'display_filename',
            $product['virtual_product_file']['nb_days_accessible'] ?? 0,
            $product['virtual_product_file']['nb_downloadable'] ?? 0,
            $product['virtual_product_file']['date_expiration'] ?? null,
        );
    }

    /**
     * @param array $product
     *
     * @return ProductStockInformation
     */
    private function createProductStockInformation(array $product): ProductStockInformation
    {
        return new ProductStockInformation(
            $product['advanced_stock_management'] ?? false,
            $product['depends_on_stock'] ?? false,
            $product['pack_stock_type'] ?? PackStockType::STOCK_TYPE_DEFAULT,
            $product['out_of_stock'] ?? OutOfStockType::OUT_OF_STOCK_DEFAULT,
            $product['quantity'] ?? static::DEFAULT_QUANTITY,
            $product['minimal_quantity'] ?? 0,
            $product['low_stock_threshold'] ?? 0,
            $product['low_stock_alert'] ?? false,
            $product['available_now'] ?? [],
            $product['available_later'] ?? [],
            $product['location'] ?? 'location',
            $product['available_date'] ?? null
        );
    }

    /**
     * @param array $product
     *
     * @return ProductSeoOptions
     */
    private function createSeoOptions(array $product): ProductSeoOptions
    {
        return new ProductSeoOptions(
            $product['meta_title'] ?? [],
            $product['meta_description'] ?? [],
            $product['link_rewrite'] ?? [],
            $product['redirect_type'] ?? RedirectType::TYPE_NOT_FOUND,
            $product['id_type_redirected'] ?? 0
        );
    }

    /**
     * @param array $product
     *
     * @return ProductShippingInformation
     */
    private function createShippingInformation(array $product): ProductShippingInformation
    {
        return new ProductShippingInformation(
            $product['width'] ?? new DecimalNumber('19.86'),
            $product['height'] ?? new DecimalNumber('19.86'),
            $product['depth'] ?? new DecimalNumber('19.86'),
            $product['weight'] ?? new DecimalNumber('19.86'),
            $product['additional_shipping_cost'] ?? new DecimalNumber('19.86'),
            $product['carrier_references'] ?? [],
            $product['delivery_time_note_type'] ?? DeliveryTimeNoteType::TYPE_DEFAULT,
            $product['delivery_time_in_stock_note'] ?? [],
            $product['delivery_time_out_stock_note'] ?? []
        );
    }

    /**
     * @param array $product
     *
     * @return ProductDetails
     */
    private function createDetails(array $product): ProductDetails
    {
        return new ProductDetails(
            $product['isbn'] ?? 'isbn',
            $product['upc'] ?? 'upc',
            $product['ean13'] ?? 'ean13',
            $product['mpn'] ?? 'mpn',
            $product['reference'] ?? 'reference'
        );
    }

    /**
     * @param array $product
     *
     * @return ProductOptions
     */
    private function createOptions(array $product): ProductOptions
    {
        return new ProductOptions(
            $product['active'] ?? true,
            $product['visibility'] ?? ProductVisibility::VISIBLE_EVERYWHERE,
            $product['available_for_order'] ?? true,
            $product['online_only'] ?? false,
            $product['show_price'] ?? true,
            $product['condition'] ?? ProductCondition::NEW,
            $product['show_condition'] ?? false,
            $product['show_condition'] ?? 0
        );
    }

    /**
     * @param array $product
     *
     * @return ProductPricesInformation
     */
    private function createPricesInformation(array $product): ProductPricesInformation
    {
        return new ProductPricesInformation(
            $product['price'] ?? new DecimalNumber('19.86'),
            $product['ecotax'] ?? new DecimalNumber('19.86'),
            $product['id_tax_rules_group'] ?? 1,
            $product['on_sale'] ?? false,
            $product['wholesale_price'] ?? new DecimalNumber('19.86'),
            $product['unit_price'] ?? new DecimalNumber('19.86'),
            $product['unity'] ?? '',
            $product['unit_price_ratio'] ?? new DecimalNumber('1'),
        );
    }

    /**
     * @param array $product
     *
     * @return ProductCategoriesInformation
     */
    private function createCategories(array $product): ProductCategoriesInformation
    {
        return new ProductCategoriesInformation(
            $product['categories'] ?? [self::DEFAULT_CATEGORY_ID],
            self::DEFAULT_CATEGORY_ID
        );
    }

    /**
     * @param array $product
     *
     * @return ProductBasicInformation
     */
    private function createBasic(array $product): ProductBasicInformation
    {
        return new ProductBasicInformation(
            new ProductType($product['type'] ?? ProductType::TYPE_STANDARD),
            $product['name'] ?? [],
            $product['description'] ?? [],
            $product['description_short'] ?? [],
            $product['tags'] ?? []
        );
    }

    /**
     * @param array $product
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|CommandBusInterface
     */
    private function createQueryBusMock(array $product)
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);

        $productForEditing = $this->createProductForEditing($product);

        $queryBusMock
            ->method('handle')
            ->with($this->isInstanceOf(GetProductForEditing::class))
            ->willReturn($productForEditing)
        ;

        return $queryBusMock;
    }

    /**
     * @return array
     */
    private function getDefaultOutputData(): array
    {
        return [
            'id' => static::PRODUCT_ID,
            'basic' => [
                'name' => [],
                'type' => ProductType::TYPE_STANDARD,
                'description' => [],
                'description_short' => [],
            ],
            'stock' => [
                'quantity' => static::DEFAULT_QUANTITY,
                'minimal_quantity' => 0,
                'stock_location' => 'location',
                'low_stock_threshold' => 0,
                'low_stock_alert' => false,
                'pack_stock_type' => PackStockType::STOCK_TYPE_DEFAULT,
                'out_of_stock_type' => OutOfStockType::OUT_OF_STOCK_DEFAULT,
                'available_now_label' => [],
                'available_later_label' => [],
                'available_date' => '',
            ],
            'price' => [
                'price_tax_excluded' => 19.86,
                'price_tax_included' => 19.86,
                'ecotax' => 19.86,
                'tax_rules_group_id' => 1,
                'on_sale' => false,
                'wholesale_price' => 19.86,
                'unit_price' => 19.86,
                'unity' => '',
            ],
            'seo' => [
                'meta_title' => [],
                'meta_description' => [],
                'link_rewrite' => [],
                'redirect' => [
                    'type' => RedirectType::TYPE_NOT_FOUND,
                    'target' => 0,
                ],
            ],
            'shipping' => [
                'width' => '19.86',
                'height' => '19.86',
                'depth' => '19.86',
                'weight' => '19.86',
                'additional_shipping_cost' => '19.86',
                'delivery_time_note_type' => DeliveryTimeNoteType::TYPE_DEFAULT,
                'delivery_time_in_stock_note' => [],
                'delivery_time_out_stock_note' => [],
                'carriers' => [],
            ],
            'options' => [
                'active' => true,
                'visibility' => ProductVisibility::VISIBLE_EVERYWHERE,
                'available_for_order' => true,
                'show_price' => true,
                'online_only' => false,
                'show_condition' => false,
                'condition' => ProductCondition::NEW,
                'tags' => [],
                'mpn' => 'mpn',
                'upc' => 'upc',
                'ean_13' => 'ean13',
                'isbn' => 'isbn',
                'reference' => 'reference',
            ],
        ];
    }
}
