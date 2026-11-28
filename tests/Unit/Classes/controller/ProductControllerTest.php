<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use Context;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Combination as CombinationAdapter;
use ProductControllerCore;
use Shop;

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

    public function testCombinationValidationDelegatesToAdapterUsingCurrentShop(): void
    {
        $combinationAdapter = $this->createMock(CombinationAdapter::class);
        $combinationAdapter
            ->expects(self::once())
            ->method('isCombinationAndAttributesAssociatedToShop')
            ->with(42, 21, 7)
            ->willReturn(true)
        ;

        $controller = new TestProductController();
        $controller->setCombinationAdapter($combinationAdapter);
        $controller->setShopId(7);

        self::assertTrue($controller->isValidCombination(42, 21));
    }

    public function testCombinationValidationRejectsInvalidIdentifiersWithoutCallingAdapter(): void
    {
        $combinationAdapter = $this->createMock(CombinationAdapter::class);
        $combinationAdapter
            ->expects(self::never())
            ->method('isCombinationAndAttributesAssociatedToShop')
        ;

        $controller = new TestProductController();
        $controller->setCombinationAdapter($combinationAdapter);

        self::assertFalse($controller->isValidCombination(0, 21));
        self::assertFalse($controller->isValidCombination(42, 0));
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
    private ?CombinationAdapter $combinationAdapter = null;

    public function __construct()
    {
    }

    public function get($serviceId)
    {
        if (CombinationAdapter::class === $serviceId && null !== $this->combinationAdapter) {
            return $this->combinationAdapter;
        }

        return parent::get($serviceId);
    }

    public function isValidCombination(int $productAttributeId, int $productId): bool
    {
        return parent::isValidCombination($productAttributeId, $productId);
    }

    public function setCombinationAdapter(CombinationAdapter $combinationAdapter): void
    {
        $this->combinationAdapter = $combinationAdapter;
    }

    public function setShopId(int $shopId): void
    {
        $this->context = new Context();
        $this->context->shop = new Shop();
        $this->context->shop->id = $shopId;
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
