<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProductGridDefinitionFactory;
use Product;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\ProductResetter;

class ProductControllerTest extends FormGridControllerTestCase
{
    private const TEST_NAME = 'testProductName';
    private const TEST_QUANTITY = 987;
    private const TEST_MINIMAL_QUANTITY = 2;
    private const TEST_RETAIL_PRICE_TAX_EXCLUDED = 87.7;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ProductResetter::resetProducts();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ProductResetter::resetProducts();
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
        $this->client->disableReboot();
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
        $this->client->disableReboot();
        // @todo: need to add dedicated tests for different product types, they all cannot be tested in one scenario,
        //       because inputs existence depends on product type
        // @todo: also the fields with disabling input doesnt seem to work in tests. The data dissappears from request.
        //        need to handle that in a future too. (inputs like: low_stock_threshold, unit_price etc..)
        // @todo: handle options like $isEcoTaxEnabled, $isTaxEnabled depending on them there are some fields that may exist or not.
        // @todo: handle relation checks like priorities, categories, related products, suppliers, attachments, features, carriers etc.
        // First update the product with a few data
        $formData = [
            'product[header][type]' => ProductType::TYPE_STANDARD,
            'product[header][initial_type]' => ProductType::TYPE_STANDARD,
            'product[header][name][1]' => self::TEST_NAME,
            'product[header][cover_thumbnail]' => 'http://myshop.com/img/p/en-default-cart_default.jpg',
            'product[header][active]' => true,
            'product[description][description][1]' => 'description 1',
            'product[description][description_short][1]' => 'description short 1',
            'product[description][manufacturer]' => 2,
            'product[details][references][mpn]' => 'mpn',
            'product[details][references][upc]' => '72527273070',
            'product[details][references][ean_13]' => '978020137962',
            'product[details][references][isbn]' => '9781234567897',
            'product[details][references][reference]' => 'reference1',
            'product[details][show_condition]' => true,
            'product[details][condition]' => ProductCondition::NEW,
            'product[shipping][dimensions][width]' => 10.4,
            'product[shipping][dimensions][height]' => 10.2,
            'product[shipping][dimensions][depth]' => 5.5,
            'product[shipping][dimensions][weight]' => 2,
            'product[shipping][additional_shipping_cost]' => 20.4,
            'product[shipping][delivery_time_note_type]' => DeliveryTimeNoteType::TYPE_SPECIFIC,
            'product[shipping][delivery_time_notes][in_stock][1]' => 'in stock notes1',
            'product[shipping][delivery_time_notes][out_of_stock][1]' => 'out of stock notes1',
            'product[options][visibility][visibility]' => ProductVisibility::VISIBLE_IN_CATALOG,
            'product[options][visibility][available_for_order]' => true,
            'product[options][visibility][show_price]' => true,
            'product[options][visibility][online_only]' => false,
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
            'product[seo][meta_title][1]' => 'meta title 1',
            'product[seo][meta_description][1]' => 'meta description 1',
            'product[seo][link_rewrite][1]' => 'link-rewrite-1',
            'product[seo][redirect_option][type]' => RedirectType::TYPE_NOT_FOUND,
            'product[seo][tags][1]' => 'tag 1, tag 2',
        ];

        $this->editEntityFromPage(['productId' => $productId], $formData);

        // Then check that it was correctly updated
        // Price is reformatted with 6 digits
        $expectedFormData = [
            'product[header][type]' => ProductType::TYPE_STANDARD,
            'product[header][initial_type]' => ProductType::TYPE_STANDARD,
            'product[header][name][1]' => self::TEST_NAME,
            'product[header][cover_thumbnail]' => 'http://myshop.com/img/p/en-default-cart_default.jpg',
            'product[header][active]' => true,
            'product[description][description][1]' => 'description 1',
            'product[description][description_short][1]' => 'description short 1',
            'product[description][manufacturer]' => 2,
            'product[details][references][mpn]' => 'mpn',
            'product[details][references][upc]' => '72527273070',
            'product[details][references][ean_13]' => '978020137962',
            'product[details][references][isbn]' => '9781234567897',
            'product[details][references][reference]' => 'reference1',
            'product[details][show_condition]' => true,
            'product[details][condition]' => ProductCondition::NEW,
            'product[shipping][dimensions][width]' => 10.4,
            'product[shipping][dimensions][height]' => 10.2,
            'product[shipping][dimensions][depth]' => 5.5,
            'product[shipping][dimensions][weight]' => 2,
            'product[shipping][additional_shipping_cost]' => 20.4,
            'product[shipping][delivery_time_note_type]' => DeliveryTimeNoteType::TYPE_SPECIFIC,
            'product[shipping][delivery_time_notes][in_stock][1]' => 'in stock notes1',
            'product[shipping][delivery_time_notes][out_of_stock][1]' => 'out of stock notes1',
            'product[options][visibility][visibility]' => ProductVisibility::VISIBLE_IN_CATALOG,
            'product[options][visibility][available_for_order]' => true,
            'product[options][visibility][show_price]' => true,
            'product[options][visibility][online_only]' => false,
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
            'product[seo][meta_title][1]' => 'meta title 1',
            'product[seo][meta_description][1]' => 'meta description 1',
            'product[seo][link_rewrite][1]' => 'link-rewrite-1',
            'product[seo][redirect_option][type]' => RedirectType::TYPE_NOT_FOUND,
            'product[seo][tags][1]' => 'tag 1,tag 2',
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
        $this->client->disableReboot();

        $products = $this->getEntitiesFromGrid();
        $initialEntityCount = $products->count();

        $this->deleteEntityFromPage('admin_products_delete_from_all_shops', ['productId' => $productId]);

        $newProducts = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount - 1, $newProducts);
    }

    /**
     * {@inheritDoc}
     */
    /**
     * The export writes its own rows instead of reusing the grid columns, so the price it writes has
     * to be the one the listing shows: ps.price, not ps.price + ecotax.
     */
    public function testExportUsesTheDisplayedPrice(): void
    {
        $product = new Product(1);
        $originalEcotax = $product->ecotax;
        $product->ecotax = 3.5;
        $product->save();

        // The export follows whatever filter the grid currently holds, and that filter is stored per
        // employee, so a test that ran before this one leaves its own filter behind - re-submitting
        // the form does not clear it, since the form renders with those values already in it.
        $this->client->request('POST', $this->router->generate(
            'admin_common_reset_search_by_filter_id',
            ['filterId' => ProductGridDefinitionFactory::GRID_ID]
        ));

        try {
            $this->client->request('GET', $this->router->generate('admin_products_export'));
            // The streamed body is already consumed by the test client, so it is read from there
            // rather than by calling sendContent() again.
            $csv = (string) $this->client->getInternalResponse()->getContent();
        } finally {
            $product->ecotax = $originalEcotax;
            $product->save();
        }

        $rows = array_map(
            static fn (string $line): array => str_getcsv($line, ';'),
            array_filter(explode("\n", trim($csv)))
        );
        $headers = array_shift($rows);
        $priceColumn = array_search('Price (tax excl.)', $headers, true);
        $this->assertNotFalse($priceColumn, 'The tax excluded price column is missing from the export');

        $exportedRow = null;
        foreach ($rows as $row) {
            if ((int) $row[0] === 1) {
                $exportedRow = $row;

                break;
            }
        }

        $this->assertNotNull($exportedRow, 'Product 1 is missing from the export');
        // The cell carries the formatted price, so only its digits are compared.
        $this->assertSame(
            number_format((float) $product->price, 2, '.', ''),
            preg_replace('/[^0-9.]/', '', $exportedRow[$priceColumn]),
            'The exported price must be the one the listing shows, without the ecotax added on top'
        );
    }

    /**
     * The listing has a status column and the export writes its own header list, so it used to drop it.
     */
    public function testExportIncludesTheStatusColumn(): void
    {
        $this->client->request('GET', $this->router->generate('admin_products_export'));
        $csv = (string) $this->client->getInternalResponse()->getContent();

        $headers = str_getcsv(strtok(trim($csv), "\n"), ';');

        $this->assertNotFalse(array_search('Status', $headers, true), 'The status column is missing from the export');
    }

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
        return $this->router->generate('admin_products_create');
    }

    /**
     * {@inheritDoc}
     */
    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_products_edit', $routeParams);
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

        return $this->router->generate('admin_products_index', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGridSelector(): string
    {
        return '#product_grid_table';
    }
}
