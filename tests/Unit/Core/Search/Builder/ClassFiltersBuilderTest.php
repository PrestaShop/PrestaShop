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
