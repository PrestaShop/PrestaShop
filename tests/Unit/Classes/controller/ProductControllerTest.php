<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use Context;
use Language;
use PHPUnit\Framework\TestCase;
use Product;
use ProductControllerCore;
use ReflectionMethod;

class ProductControllerTest extends TestCase
{
    /**
     * @dataProvider productFeaturesProvider
     */
    public function testItAddsProductFeaturesToStructuredData(array $features, array $expectedAdditionalProperties): void
    {
        // Add the supplied product features to an otherwise empty product schema
        $structuredProductData = ['@type' => 'Product'];
        (new TestProductController())->addProductFeaturesToStructuredData($structuredProductData, $features);

        // Verify that only complete normalized properties were added
        if (empty($expectedAdditionalProperties)) {
            $this->assertArrayNotHasKey('additionalProperty', $structuredProductData);

            return;
        }

        $this->assertSame($expectedAdditionalProperties, $structuredProductData['additionalProperty']);
    }

    public function testItAppendsProductFeaturesToExistingAdditionalProperties(): void
    {
        // Prepare a product schema that was already enriched by another source
        $structuredProductData = [
            '@type' => 'Product',
            'additionalProperty' => [
                [
                    '@type' => 'PropertyValue',
                    'name' => 'Existing property',
                    'value' => 'Existing value',
                ],
            ],
        ];

        // Add another product feature without replacing the existing property
        (new TestProductController())->addProductFeaturesToStructuredData($structuredProductData, [
            ['name' => 'Material', 'value' => 'Granite'],
        ]);

        $this->assertSame([
            [
                '@type' => 'PropertyValue',
                'name' => 'Existing property',
                'value' => 'Existing value',
            ],
            [
                '@type' => 'PropertyValue',
                'name' => 'Material',
                'value' => 'Granite',
            ],
        ], $structuredProductData['additionalProperty']);
    }

    public function testItLooksUpEachCombinationOnce(): void
    {
        $product = new CountingProduct([4 => [['id_product_attribute' => 4, 'minimal_quantity' => 3]]]);
        $controller = new TestProductController();
        $controller->setProductAndLanguage($product);

        $this->assertSame(['id_product_attribute' => 4, 'minimal_quantity' => 3], $controller->findProductCombinationById(4));
        $this->assertSame(['id_product_attribute' => 4, 'minimal_quantity' => 3], $controller->findProductCombinationById(4));
        $this->assertNull($controller->findProductCombinationById(5));
        $this->assertNull($controller->findProductCombinationById(5));

        $this->assertSame([4, 5], $product->attributesGroupsLookups);
    }

    public function testItResolvesTheCombinationOfAProductWithoutCombinationsOnce(): void
    {
        $product = new CountingProduct([]);
        $controller = new TestProductController();
        $controller->setProductAndLanguage($product);
        $resolve = new ReflectionMethod(ProductControllerCore::class, 'getIdProductAttributeByGroupOrRequestOrDefault');

        $this->assertNull($resolve->invoke($controller));
        $this->assertNull($resolve->invoke($controller));

        $this->assertSame(1, $product->hasCombinationsLookups);
    }

    public static function productFeaturesProvider(): iterable
    {
        yield 'no features' => [
            [],
            [],
        ];

        yield 'complete feature' => [
            [['name' => 'Material', 'value' => 'Granite']],
            [[
                '@type' => 'PropertyValue',
                'name' => 'Material',
                'value' => 'Granite',
            ]],
        ];

        yield 'surrounding whitespace' => [
            [['name' => '  Material  ', 'value' => '  Granite  ']],
            [[
                '@type' => 'PropertyValue',
                'name' => 'Material',
                'value' => 'Granite',
            ]],
        ];

        yield 'grouped multiline values' => [
            [['name' => 'Installation method', 'value' => "Top\r\nBottom\nFlush"]],
            [[
                '@type' => 'PropertyValue',
                'name' => 'Installation method',
                'value' => 'Top, Bottom, Flush',
            ]],
        ];

        yield 'numeric strings' => [
            [['name' => 123, 'value' => 600]],
            [[
                '@type' => 'PropertyValue',
                'name' => '123',
                'value' => '600',
            ]],
        ];

        yield 'missing name' => [
            [['value' => 'Granite']],
            [],
        ];

        yield 'missing value' => [
            [['name' => 'Material']],
            [],
        ];

        yield 'null values' => [
            [['name' => null, 'value' => null]],
            [],
        ];

        yield 'empty strings' => [
            [['name' => '', 'value' => '']],
            [],
        ];

        yield 'whitespace only strings' => [
            [['name' => '  ', 'value' => " \r\n "]],
            [],
        ];

        yield 'false values' => [
            [
                ['name' => false, 'value' => 'Value'],
                ['name' => 'Name', 'value' => false],
            ],
            [],
        ];

        yield 'zero values' => [
            [
                ['name' => 'Numeric zero', 'value' => 0],
                ['name' => 'String zero', 'value' => '0'],
            ],
            [
                [
                    '@type' => 'PropertyValue',
                    'name' => 'Numeric zero',
                    'value' => '0',
                ],
                [
                    '@type' => 'PropertyValue',
                    'name' => 'String zero',
                    'value' => '0',
                ],
            ],
        ];

        yield 'mixed complete and incomplete features' => [
            [
                ['name' => '', 'value' => 'Ignored'],
                ['name' => 'Material', 'value' => 'Granite'],
                ['name' => 'Ignored', 'value' => null],
                ['name' => 'Minimum cabinet width', 'value' => '600 mm'],
            ],
            [
                [
                    '@type' => 'PropertyValue',
                    'name' => 'Material',
                    'value' => 'Granite',
                ],
                [
                    '@type' => 'PropertyValue',
                    'name' => 'Minimum cabinet width',
                    'value' => '600 mm',
                ],
            ],
        ];
    }
}

class TestProductController extends ProductControllerCore
{
    public function __construct()
    {
    }

    public function setProductAndLanguage(Product $product): void
    {
        $language = new class() extends Language {
            public function __construct()
            {
            }
        };
        $language->id = 1;
        $this->context = new Context();
        $this->context->language = $language;
        $this->product = $product;
    }

    /**
     * Adds product features to structured data for testing.
     *
     * @param array $structuredProductData
     * @param array $features
     */
    public function addProductFeaturesToStructuredData(array &$structuredProductData, array $features): void
    {
        parent::addProductFeaturesToStructuredData($structuredProductData, $features);
    }
}

class CountingProduct extends Product
{
    /** @var int[] */
    public array $attributesGroupsLookups = [];

    public int $hasCombinationsLookups = 0;

    /**
     * @param array<int, array<int, array<string, mixed>>> $combinations Rows returned per combination id
     */
    public function __construct(private array $combinations)
    {
        $this->id = 1;
    }

    public function getAttributesGroups($id_lang, $id_product_attribute = null)
    {
        $this->attributesGroupsLookups[] = (int) $id_product_attribute;

        return $this->combinations[(int) $id_product_attribute] ?? [];
    }

    public function hasCombinations()
    {
        ++$this->hasCombinationsLookups;

        return !empty($this->combinations);
    }
}
