<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Search\Builder\TypedBuilder;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder\ProductCombinationFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class ProductCombinationFiltersBuilderTest extends TestCase
{
    /**
     * @dataProvider getSupportsValues
     *
     * @param string $filtersClass
     * @param bool $expectedSupport
     */
    public function testSupports(string $filtersClass, bool $expectedSupport)
    {
        $builder = new ProductCombinationFiltersBuilder();
        $isSupported = $builder->supports($filtersClass);
        $this->assertEquals($expectedSupport, $isSupported);
    }

    public function testBuildFilters()
    {
        $productId = 42;
        $shopId = 2;
        $builder = new ProductCombinationFiltersBuilder();
        $builder->setConfig(['request' => $this->buildRequestMock($productId, $shopId)]);

        $builtFilters = $builder->buildFilters();
        $this->assertEquals(ProductCombinationFilters::generateFilterId($productId), $builtFilters->getFilterId());
        $filters = $builtFilters->getFilters();
        $this->assertEquals($productId, $filters['product_id']);
    }

    public function testBuildFiltersWithInitialValues()
    {
        $productId = 42;
        $shopId = 2;
        $builder = new ProductCombinationFiltersBuilder();
        $builder->setConfig(['request' => $this->buildRequestMock($productId, $shopId)]);

        $initialFilters = new Filters([
            'filters' => [
                'product_id' => 51,
                'category_id' => 45,
            ],
        ], 'product_id');

        $builtFilters = $builder->buildFilters($initialFilters);
        $this->assertEquals(ProductCombinationFilters::generateFilterId($productId), $builtFilters->getFilterId());
        $filters = $builtFilters->getFilters();
        $this->assertEquals($productId, $filters['product_id']);
        $this->assertEquals(45, $filters['category_id']);
    }

    public function getSupportsValues(): Generator
    {
        yield [
            ProductCombinationFilters::class,
            true,
        ];

        yield [
            '',
            false,
        ];

        yield [
            Filters::class,
            false,
        ];
    }

    /**
     * @param int $productId
     *
     * @return Request
     */
    private function buildRequestMock(int $productId, int $shopId): Request
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $parameterBagMock = new InputBag();
        $parameterBagMock->replace([
            'shopId' => $shopId,
            'productId' => $productId,
        ]);

        $requestMock->attributes = $parameterBagMock;
        $requestMock->query = $parameterBagMock;

        return $requestMock;
    }
}
