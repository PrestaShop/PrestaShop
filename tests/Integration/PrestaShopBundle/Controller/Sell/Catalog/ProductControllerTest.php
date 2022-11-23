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

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\ProductResetter;

class ProductControllerTest extends FormGridControllerTestCase
{
    private const TEST_NAME = 'testProductName';
    private const TEST_QUANTITY = 987;
    private const TEST_MINIMAL_QUANTITY = 2;
    private const TEST_RETAIL_PRICE_TAX_EXCLUDED = 87.7;

    /**
     * @var bool
     */
    private $changedProductFeatureFlag = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::mockContext();
        ProductResetter::resetProducts();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ProductResetter::resetProducts();
    }

    public function setUp(): void
    {
        parent::setUp();
        $featureFlagRepository = $this->client->getContainer()->get('prestashop.core.admin.feature_flag.repository');
        if (!$featureFlagRepository->isEnabled(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2)) {
            $featureFlagRepository->enable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);
            $this->changedProductFeatureFlag = true;
        }
    }

    public function tearDown(): void
    {
        if ($this->changedProductFeatureFlag) {
            $featureFlagRepository = $this->client->getContainer()->get('prestashop.core.admin.feature_flag.repository');
            $featureFlagRepository->disable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);
        }

        // Call parent tear down later or the kernel will be shut down
        parent::tearDown();
    }

    public function testIndex(): int
    {
        $products = $this->getEntitiesFromGrid();
        $this->assertNotEmpty($products);

        return $products->count();
    }

    /**
     * @depends testIndex
     *
     * @param int $initialEntityCount
     *
     * @return int
     */
    public function testCreate(int $initialEntityCount): int
    {
        // First create product
        $formData = [
            'create_product[type]' => ProductType::TYPE_STANDARD,
        ];
        $createdProductId = $this->createEntityFromPage($formData);
        $this->assertNotNull($createdProductId);

        // Check that there is one more product in the list
        $newProducts = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount + 1, $newProducts);
        $this->assertCollectionContainsEntity($newProducts, $createdProductId);

        // Check that the product was correctly set with the expected type (the format in the form is not the same)
        $expectedFormData = [
            'product[header][type]' => ProductType::TYPE_STANDARD,
        ];
        $this->assertFormValuesFromPage(
            ['productId' => $createdProductId],
            $expectedFormData
        );

        return $createdProductId;
    }

    /**
     * @depends testCreate
     *
     * @param int $productId
     *
     * @return int
     */
    public function testEdit(int $productId): int
    {
        // @todo: need to add dedicated tests for different product types, they all cannot be tested in one scenario,
        //       because inputs existence depends on product type
        // @todo: also the fields with disabling input doesnt seem to work in tests. The data dissappears from request.
        //        need to handle that in a future too. (inputs like: low_stock_threshold, unit_price etc..)
        // @todo: handle options like $isEcoTaxEnabled, $isTaxEnabled depending on them there are some fields that may exist or not.
        // @todo: handle relation checks like specific price priorities
        // First update the product with a few data
        $formData = [
            'product[header][name][1]' => self::TEST_NAME,
            'product[stock][quantities][delta_quantity][delta]' => self::TEST_QUANTITY,
            'product[stock][quantities][minimal_quantity]' => self::TEST_MINIMAL_QUANTITY,
            'product[stock][options][stock_location]' => 'test stock location',
            'product[stock][availability][out_of_stock_type]' => OutOfStockType::OUT_OF_STOCK_DEFAULT,
            'product[stock][availability][available_now_label][1]' => 'Available now',
            'product[stock][availability][available_later_label][1]' => 'Available later',
            'product[stock][availability][available_date]' => '2022-11-11',
            'product[pricing][retail_price][price_tax_excluded]' => self::TEST_RETAIL_PRICE_TAX_EXCLUDED,
            // tax included value should be calculated for viewing only, so it doesn't matter what its value is before submit
            'product[pricing][retail_price][price_tax_included]' => 1992491249214,
            'product[pricing][retail_price][tax_rules_group_id]' => 1,
            'product[pricing][on_sale]' => false,
            'product[pricing][wholesale_price]' => 30.5,
        ];

        $this->editEntityFromPage(['productId' => $productId], $formData);

        // Then check that it was correctly updated
        // Price is reformatted with 6 digits
        $expectedFormData = [
            'product[header][name][1]' => self::TEST_NAME,
            'product[stock][quantities][delta_quantity][delta]' => 0,
            'product[stock][quantities][delta_quantity][quantity]' => 987,
            'product[stock][quantities][minimal_quantity]' => self::TEST_MINIMAL_QUANTITY,
            'product[stock][options][stock_location]' => 'test stock location',
            'product[stock][availability][out_of_stock_type]' => OutOfStockType::OUT_OF_STOCK_DEFAULT,
            'product[stock][availability][available_now_label][1]' => 'Available now',
            'product[stock][availability][available_later_label][1]' => 'Available later',
            'product[stock][availability][available_date]' => '2022-11-11',
            'product[pricing][retail_price][price_tax_excluded]' => self::TEST_RETAIL_PRICE_TAX_EXCLUDED,
            'product[pricing][retail_price][tax_rules_group_id]' => 1,
            // tax rules group with id 1 value is 4%, so tax incl = retail_price_tax_excluded + (retail_price_tax_excluded*0.04)
            'product[pricing][retail_price][price_tax_included]' => 91.208,
            'product[pricing][on_sale]' => false,
            'product[pricing][wholesale_price]' => 30.5,
        ];
        $this->assertFormValuesFromPage(
            ['productId' => $productId],
            $expectedFormData
        );

        return $productId;
    }

    /**
     * @depends testEdit
     *
     * @param int $productId
     *
     * @return int
     */
    public function testFilters(int $productId): int
    {
        // These are filters which are only supposed to match the created product, they might need to be updated
        // in case the data from fixtures change and match these test data.
        $gridFilters = [
            [
                'product[name]' => self::TEST_NAME,
            ],
            [
                'product[id_product][min_field]' => $productId,
                'product[id_product][max_field]' => $productId,
            ],
            [
                'product[quantity][min_field]' => self::TEST_QUANTITY,
                'product[quantity][max_field]' => self::TEST_QUANTITY,
            ],
            [
                'product[final_price_tax_excluded][min_field]' => self::TEST_RETAIL_PRICE_TAX_EXCLUDED,
                'product[final_price_tax_excluded][max_field]' => self::TEST_RETAIL_PRICE_TAX_EXCLUDED,
            ],
        ];

        foreach ($gridFilters as $testFilter) {
            $products = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertGreaterThanOrEqual(1, count($products), sprintf(
                'Expected at least one product with filters %s',
                var_export($testFilter, true)
            ));
            $this->assertCollectionContainsEntity($products, $productId);
        }

        return $productId;
    }

    /**
     * @depends testFilters
     *
     * @param int $productId
     */
    public function testDelete(int $productId): void
    {
        $products = $this->getEntitiesFromGrid();
        $initialEntityCount = $products->count();

        $this->deleteEntityFromPage('admin_products_v2_delete', ['productId' => $productId]);

        $newProducts = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount - 1, $newProducts);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilterSearchButtonSelector(): string
    {
        return 'product[actions][search]';
    }

    /**
     * {@inheritDoc}
     */
    protected function getCreateSubmitButtonSelector(): string
    {
        return 'create_product_create';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditSubmitButtonSelector(): string
    {
        return 'product_footer_save';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateCreateUrl(): string
    {
        return $this->router->generate('admin_products_v2_create');
    }

    /**
     * {@inheritDoc}
     */
    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_products_v2_edit', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.product_form_handler');

        return $checker;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_product')->text()),
           [
           ]
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'product[offset]' => 0,
                'product[limit]' => 100,
            ];
        }

        return $this->router->generate('admin_products_v2_index', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGridSelector(): string
    {
        return '#product_grid_table';
    }
}
