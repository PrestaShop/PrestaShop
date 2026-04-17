<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Search\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Builder\ClassFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Tests\Resources\SampleFilters;
use Tests\Resources\SampleShopFilters;

class ClassFiltersBuilderTest extends TestCase
{
    public function testBuildWithoutClass()
    {
        $builder = new ClassFiltersBuilder();
        $filters = $builder->buildFilters();
        $this->assertNull($filters);
    }

    public function testOverrideWithoutClass()
    {
        $builder = new ClassFiltersBuilder();
        $filters = new Filters(['limit' => 51]);
        $builtFilters = $builder->buildFilters($filters);
        $this->assertNotNull($builtFilters);
        $this->assertEquals($filters->all(), $builtFilters->all());
        $this->assertEmpty($builtFilters->getFilterId());
    }

    public function testBuildWithClass()
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleFilters::class]);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        $this->assertEquals(SampleFilters::getDefaults(), $filters->all());
        $this->assertEmpty($filters->getFilterId());
        $this->assertInstanceOf(SampleFilters::class, $filters);
    }

    public function testBuildWithClassAndFilterId()
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleFilters::class, 'filter_id' => 'language']);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        $this->assertEquals(SampleFilters::getDefaults(), $filters->all());
        $this->assertEquals('language', $filters->getFilterId());
        $this->assertInstanceOf(SampleFilters::class, $filters);
    }

    public function testOverrideWithClass()
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleFilters::class]);
        $filters = new Filters(['limit' => 10]);
        $builtFilters = $builder->buildFilters($filters);
        $this->assertNotNull($builtFilters);
        $this->assertEquals(SampleFilters::getDefaults(), $builtFilters->all());
        $this->assertEmpty($builtFilters->getFilterId());
        $this->assertInstanceOf(SampleFilters::class, $builtFilters);
    }

    /**
     * @dataProvider getShopConstraints
     *
     * @param $shopConstraint
     */
    public function testCreateWithShopConstraint($shopConstraint, $expectedShopConstraint): void
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleShopFilters::class, 'shop_constraint' => $shopConstraint]);

        $builtFilters = $builder->buildFilters();

        $this->assertNotNull($builtFilters);
        $this->assertEquals(SampleShopFilters::getDefaults(), $builtFilters->all());
        $this->assertInstanceOf(SampleShopFilters::class, $builtFilters);
        if ($builtFilters instanceof SampleShopFilters) {
            $this->assertEquals($expectedShopConstraint, $builtFilters->getShopConstraint());
        }
    }

    /**
     * @dataProvider getShopConstraints
     *
     * @param $shopConstraint
     */
    public function testUpdateWithShopConstraint($shopConstraint, $expectedShopConstraint): void
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleShopFilters::class, 'shop_constraint' => $shopConstraint]);

        $initialFilters = new SampleShopFilters($shopConstraint, ['limit' => 456], 'shopId');
        $builtFilters = $builder->buildFilters($initialFilters);

        $this->assertNotNull($builtFilters);
        $this->assertEquals(SampleShopFilters::getDefaults(), $builtFilters->all());
        $this->assertInstanceOf(SampleShopFilters::class, $builtFilters);
        if ($builtFilters instanceof SampleShopFilters) {
            $this->assertEquals($expectedShopConstraint, $builtFilters->getShopConstraint());
        }
    }

    public function getShopConstraints(): iterable
    {
        $constraint = ShopConstraint::shop(42);
        yield 'single shop constraint' => [$constraint, $constraint];

        $constraint = ShopConstraint::shopGroup(42);
        yield 'group shop constraint' => [$constraint, $constraint];

        $constraint = ShopConstraint::allShops();
        yield 'all shop constraint' => [$constraint, $constraint];
    }
}
