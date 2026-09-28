<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Definition;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;

final class ExtraPropertyDefinitionCollectionTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Construction, iteration, inspection
    // -------------------------------------------------------------------------

    public function testConstructorReindexesTheDefinitions(): void
    {
        $collection = new ExtraPropertyDefinitionCollection([5 => $this->definition('a'), 9 => $this->definition('b')]); // @phpstan-ignore-line intentionally not a list

        $this->assertSame([0, 1], array_keys(iterator_to_array($collection)));
        $this->assertSame(['a', 'b'], $this->names($collection));
    }

    public function testCountIterationAndFirst(): void
    {
        $a = $this->definition('a');
        $collection = new ExtraPropertyDefinitionCollection([$a, $this->definition('b'), $this->definition('c')]);

        $this->assertCount(3, $collection);
        $this->assertFalse($collection->isEmpty());
        $this->assertSame($a, $collection->first());
        $this->assertSame(['a', 'b', 'c'], $this->names($collection));
    }

    public function testEmptyCollection(): void
    {
        $collection = new ExtraPropertyDefinitionCollection([]);

        $this->assertCount(0, $collection);
        $this->assertTrue($collection->isEmpty());
        $this->assertNull($collection->first());
        $this->assertSame([], iterator_to_array($collection));
    }

    public function testEmptyIsASharedInstance(): void
    {
        $empty = ExtraPropertyDefinitionCollection::empty();

        $this->assertSame($empty, ExtraPropertyDefinitionCollection::empty());
        $this->assertTrue($empty->isEmpty());
        $this->assertCount(0, $empty);
    }

    // -------------------------------------------------------------------------
    // Filters
    // -------------------------------------------------------------------------

    /**
     * Public API without core caller. null, '' and '_core' all select core definitions
     * (whose module name is normalized to null at construction).
     *
     * @dataProvider moduleNameFilterProvider
     */
    public function testFilterByModuleName(?string $moduleName, array $expected): void
    {
        $this->assertSame($expected, $this->names($this->fixture()->filterByModuleName($moduleName)));
    }

    public static function moduleNameFilterProvider(): iterable
    {
        yield 'null selects core definitions' => [null, ['core_product', 'core_sentinel']];
        yield 'empty string selects core definitions' => ['', ['core_product', 'core_sentinel']];
        yield '_core sentinel selects core definitions' => [ExtraPropertyDefinition::CORE_MODULE_KEY, ['core_product', 'core_sentinel']];
        yield 'module name' => ['mod_one', ['one_product_lang', 'one_category']];
        yield 'another module' => ['mod_two', ['two_combination_shop']];
        yield 'unknown module' => ['mod_unknown', []];
    }

    /**
     * @dataProvider scopeFilterProvider
     */
    public function testFilterByScope(ExtraPropertyScope $scope, array $expected): void
    {
        $this->assertSame($expected, $this->names($this->fixture()->filterByScope($scope)));
    }

    public static function scopeFilterProvider(): iterable
    {
        yield 'common' => [ExtraPropertyScope::COMMON, ['core_product', 'core_sentinel', 'one_category']];
        yield 'lang' => [ExtraPropertyScope::LANG, ['one_product_lang']];
        yield 'shop' => [ExtraPropertyScope::SHOP, ['two_combination_shop']];
    }

    /**
     * Logical entity vs physical table differ for irregular entities ('combination' / 'product_attribute').
     */
    public function testFilterByEntityMatchesTheLogicalEntityName(): void
    {
        $collection = $this->fixture();

        $this->assertSame(['core_product', 'core_sentinel', 'one_product_lang'], $this->names($collection->filterByEntity('product')));
        $this->assertSame(['two_combination_shop'], $this->names($collection->filterByEntity('combination')));
        $this->assertSame([], $this->names($collection->filterByEntity('product_attribute')));
    }

    public function testFilterByTableNameMatchesThePhysicalTable(): void
    {
        $collection = $this->fixture();

        $this->assertSame(['two_combination_shop'], $this->names($collection->filterByTableName('product_attribute')));
        $this->assertSame([], $this->names($collection->filterByTableName('combination')));
        $this->assertSame(['one_category'], $this->names($collection->filterByTableName('category')));
    }

    public function testFilterByForm(): void
    {
        $collection = $this->fixture();

        $this->assertSame(['core_product', 'one_product_lang'], $this->names($collection->filterByForm('product')));
        $this->assertSame(['one_category'], $this->names($collection->filterByForm('category')));
        $this->assertSame([], $this->names($collection->filterByForm('customer')));
    }

    public function testFilterByGrid(): void
    {
        $collection = $this->fixture();

        $this->assertSame(['core_product'], $this->names($collection->filterByGrid('product')));
        $this->assertSame(['two_combination_shop'], $this->names($collection->filterByGrid('combination')));
        $this->assertSame([], $this->names($collection->filterByGrid('category')));
    }

    public function testFilterForFrontOffice(): void
    {
        $this->assertSame(['core_product', 'one_category'], $this->names($this->fixture()->filterForFrontOffice()));
    }

    /**
     * @dataProvider apiFilterProvider
     */
    public function testFilterByApi(string $uriTemplate, string $method, array $expected): void
    {
        $this->assertSame($expected, $this->names($this->fixture()->filterByApi($uriTemplate, $method)));
    }

    public static function apiFilterProvider(): iterable
    {
        yield 'method-restricted entry' => ['/products/{productId}', 'get', ['core_product', 'one_product_lang']];
        yield 'unlisted method keeps only the unrestricted entry' => ['/products/{productId}', 'DELETE', ['one_product_lang']];
        yield 'other template' => ['/products/combinations/{combinationId}', 'PATCH', ['two_combination_shop']];
        yield 'unknown template' => ['/customers/{customerId}', 'GET', []];
    }

    /**
     * Rules themselves are covered by ExtraPropertyDefinitionShopFilterTest; this pins the per-module lookup.
     */
    public function testFilterByShopsLooksUpTheOwningModuleShops(): void
    {
        $collection = $this->fixture();

        // core_product is restricted to shop 1; core_sentinel is core and unrestricted;
        // mod_one is enabled on shop 1 only; mod_two has no entry (unknown, so unrestricted).
        $this->assertSame(
            ['core_sentinel', 'two_combination_shop'],
            $this->names($collection->filterByShops([2], ['mod_one' => [1]]))
        );
        $this->assertSame(
            ['core_product', 'core_sentinel', 'one_product_lang', 'one_category', 'two_combination_shop'],
            $this->names($collection->filterByShops([1], ['mod_one' => [1]]))
        );
        // Without any module association every module-owned definition stays available.
        $this->assertSame(
            ['core_sentinel', 'one_product_lang', 'one_category', 'two_combination_shop'],
            $this->names($collection->filterByShops([3]))
        );
    }

    public function testFiltersReturnNewInstancesAndChain(): void
    {
        $collection = $this->fixture();

        $filtered = $collection->filterByEntity('product')->filterByModuleName('mod_one')->filterByForm('product');

        $this->assertNotSame($collection, $collection->filterByScope(ExtraPropertyScope::COMMON));
        $this->assertSame(['one_product_lang'], $this->names($filtered));
        $this->assertCount(5, $collection);
    }

    private function fixture(): ExtraPropertyDefinitionCollection
    {
        return new ExtraPropertyDefinitionCollection([
            new ExtraPropertyDefinition(
                entityName: 'product',
                propertyName: 'core_product',
                displayFront: true,
                associatedForms: ['product:options'],
                associatedGrids: ['product:reference'],
                associatedApis: ['/products/{productId}:GET'],
                labelWording: 'Core product',
                associatedShopIds: [1],
            ),
            // The '_core' sentinel is normalized to a core definition at construction.
            new ExtraPropertyDefinition(
                entityName: 'product',
                propertyName: 'core_sentinel',
                moduleName: ExtraPropertyDefinition::CORE_MODULE_KEY,
            ),
            new ExtraPropertyDefinition(
                entityName: 'product',
                propertyName: 'one_product_lang',
                scope: ExtraPropertyScope::LANG,
                moduleName: 'mod_one',
                associatedForms: ['product'],
                associatedApis: ['/products/{productId}'],
                labelWording: 'Module one product',
            ),
            new ExtraPropertyDefinition(
                entityName: 'category',
                propertyName: 'one_category',
                moduleName: 'mod_one',
                displayFront: true,
                associatedForms: ['category:description:after'],
                labelWording: 'Module one category',
            ),
            new ExtraPropertyDefinition(
                entityName: 'combination',
                propertyName: 'two_combination_shop',
                scope: ExtraPropertyScope::SHOP,
                moduleName: 'mod_two',
                associatedGrids: ['combination'],
                associatedApis: ['/products/combinations/{combinationId}:GET,PATCH'],
                labelWording: 'Module two combination',
            ),
        ]);
    }

    private function definition(string $propertyName): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(entityName: 'product', propertyName: $propertyName);
    }

    /**
     * @return list<string>
     */
    private function names(ExtraPropertyDefinitionCollection $collection): array
    {
        return array_map(
            static fn (ExtraPropertyDefinition $definition): string => $definition->getPropertyName(),
            iterator_to_array($collection, false)
        );
    }
}
