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
use PrestaShop\PrestaShop\Core\Search\Builder\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Builder\ClassFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder\TypedFiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use RuntimeException;

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

    public function testTypedBuilders()
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleFilters::class]);
        $filters = new Filters(['limit' => 10]);

        $builtFilters = $builder->buildFilters($filters);
        $this->assertEmpty($builtFilters->getFilterId());
        $this->assertEquals('id_sample', $builtFilters->getOrderBy());

        $builder->addTypedBuilder(new SampleFiltersBuilder());

        $builtFilters = $builder->buildFilters($filters);
        $this->assertEquals(SampleFiltersBuilder::FILTER_ID, $builtFilters->getFilterId());
        $this->assertEquals(SampleFiltersBuilder::ORDER_BY, $builtFilters->getOrderBy());
    }

    /**
     * Since SampleWithConstraintFilters does not allow empty filterId if ClassFiltersBuilders
     * tries to construct it an error will be thrown.
     */
    public function testConstraintFilters()
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleWithConstraintFilters::class]);

        $this->expectExceptionMessage('Cannot be constructed without filterId');
        $this->expectException(RuntimeException::class);
        $builder->buildFilters();
    }

    /**
     * Since SampleWithConstraintFilters does not allow empty filterId if ClassFiltersBuilders
     * tries to construct it an error will be thrown. With our associated builder we ensure it
     * is correctly built, and only built once.
     */
    public function testOnlyBuilderCreatesFilters()
    {
        $builder = new ClassFiltersBuilder();
        $builder->setConfig(['filters_class' => SampleWithConstraintFilters::class]);

        $builder->addTypedBuilder(new SampleFiltersBuilder());

        $builtFilters = $builder->buildFilters();
        $this->assertEquals(SampleFiltersBuilder::FILTER_ID, $builtFilters->getFilterId());
        $this->assertEquals(SampleFiltersBuilder::ORDER_BY, $builtFilters->getOrderBy());
    }
}

class SampleFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 42,
            'offset' => 0,
            'orderBy' => 'id_sample',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}

class SampleWithConstraintFilters extends Filters
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $filters = [], $filterId = '')
    {
        if (empty($filterId)) {
            throw new RuntimeException('Cannot be constructed without filterId');
        }
        parent::__construct($filters, $filterId);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 42,
            'offset' => 0,
            'orderBy' => 'id_sample',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}

class SampleFiltersBuilder extends AbstractFiltersBuilder implements TypedFiltersBuilderInterface
{
    public const FILTER_ID = 'specialId';
    public const ORDER_BY = 'id_special';

    /**
     * @var string
     */
    private $filtersClass;

    /**
     * {@inheritDoc}
     */
    public function setConfig(array $config)
    {
        $this->filtersClass = $config['filters_class'];

        return parent::setConfig($config);
    }

    /**
     * {@inheritDoc}
     */
    public function buildFilters(Filters $filters = null)
    {
        return new $this->filtersClass(['orderBy' => self::ORDER_BY], self::FILTER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $filterClassName): bool
    {
        return
            SampleWithConstraintFilters::class === $filterClassName
            || SampleFilters::class === $filterClassName
        ;
    }
}
