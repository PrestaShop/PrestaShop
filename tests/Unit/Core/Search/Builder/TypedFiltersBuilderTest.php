<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Search\Builder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Builder\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Builder\FiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder\TypedFiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\TypedFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use RuntimeException;

class TypedFiltersBuilderTest extends TestCase
{
    /**
     * Ensure that provided typed builders a re indeed used by checking their support function
     * is called.
     */
    public function testBuildersInConstructorAreUsed()
    {
        $config = ['filters_class' => SampleWithoutConstraintFilters::class];

        $mock1 = $this->createTypeBuilderMock($config);
        $mock2 = $this->createTypeBuilderMock($config);
        $typedBuilders = [
            $mock1,
            $mock2,
        ];
        $filters = new Filters(['limit' => 10]);
        $builder = new TypedFiltersBuilder($this->createBuilderMock($filters), $typedBuilders);
        $builder->setConfig($config);

        $builtFilters = $builder->buildFilters($filters);
        $this->assertEmpty($builtFilters->getFilterId());
        $this->assertEquals('id_sample', $builtFilters->getOrderBy());
    }

    public function testBuildersAddedAreUsed()
    {
        $config = ['filters_class' => SampleWithoutConstraintFilters::class];

        $filters = new Filters(['limit' => 10]);
        $builder = new TypedFiltersBuilder($this->createBuilderMock($filters));
        $builder->setConfig($config);

        $mock1 = $this->createTypeBuilderMock($config);
        $mock2 = $this->createTypeBuilderMock($config);
        $typedBuilders = [
            $mock1,
            $mock2,
        ];
        foreach ($typedBuilders as $typedBuilder) {
            $builder->addTypedBuilder($typedBuilder);
        }

        $builtFilters = $builder->buildFilters($filters);
        $this->assertEmpty($builtFilters->getFilterId());
        $this->assertEquals('id_sample', $builtFilters->getOrderBy());
    }

    /**
     * When no config is set, it can't be set to sub builders and there is no use checking
     * compatibility of a non defined class.
     */
    public function testWithoutConfig()
    {
        $mock1 = $this->createTypeBuilderMock(null);
        $mock2 = $this->createTypeBuilderMock(null);
        $typedBuilders = [
            $mock1,
            $mock2,
        ];
        $filters = new Filters(['limit' => 10]);
        $builder = new TypedFiltersBuilder($this->createBuilderMock($filters), $typedBuilders);

        $builtFilters = $builder->buildFilters($filters);
        $this->assertEmpty($builtFilters->getFilterId());
        $this->assertEquals('id_sample', $builtFilters->getOrderBy());
    }

    public function testBuildFilters()
    {
        $filters = new Filters(['limit' => 10]);
        $builder = new TypedFiltersBuilder($this->createBuilderMock($filters));
        $builder->setConfig(['filters_class' => SampleWithoutConstraintFilters::class]);

        $builtFilters = $builder->buildFilters($filters);
        $this->assertEmpty($builtFilters->getFilterId());
        $this->assertEquals('id_sample', $builtFilters->getOrderBy());

        $builder->addTypedBuilder(new SampleFiltersBuilder());

        $builtFilters = $builder->buildFilters($filters);
        $this->assertEquals(SampleFiltersBuilder::FILTER_ID, $builtFilters->getFilterId());
        $this->assertEquals(SampleFiltersBuilder::ORDER_BY, $builtFilters->getOrderBy());
    }

    /**
     * Only the matching builder must be used (which is ensured thanks to the mock whose buildFilters
     * method must NEVER be called).
     */
    public function testOnlyMatchingBuilderCreatesFilters()
    {
        $builder = new TypedFiltersBuilder($this->createBuilderMock(null));
        $builder->setConfig(['filters_class' => SampleWithConstraintFilters::class]);

        $builder->addTypedBuilder(new SampleFiltersBuilder());

        $builtFilters = $builder->buildFilters();
        $this->assertEquals(SampleFiltersBuilder::FILTER_ID, $builtFilters->getFilterId());
        $this->assertEquals(SampleFiltersBuilder::ORDER_BY, $builtFilters->getOrderBy());
    }

    /**
     * @param Filters|null $filters
     *
     * @return MockObject|FiltersBuilderInterface
     */
    private function createBuilderMock(?Filters $filters)
    {
        $builderMock = $this
            ->getMockBuilder(FiltersBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builtFilters = null;
        if (null !== $filters) {
            $builtFilters = new SampleWithoutConstraintFilters(
                array_replace(SampleWithoutConstraintFilters::getDefaults(), $filters->all())
            );
        }

        $builderMock
            ->expects(null !== $filters ? $this->once() : $this->never())
            ->method('buildFilters')
            ->willReturn($builtFilters)
        ;

        return $builderMock;
    }

    /**
     * @param array|null $expectedConfig
     *
     * @return MockObject|TypedFiltersBuilderInterface
     */
    private function createTypeBuilderMock(?array $expectedConfig)
    {
        $builderMock = $this
            ->getMockBuilder(TypedFiltersBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builderMock
            ->expects(null === $expectedConfig ? $this->never() : $this->once())
            ->method('supports')
            ->with($this->equalTo(SampleWithoutConstraintFilters::class))
            ->willReturn(false)
        ;

        $builderMock
            ->expects(null === $expectedConfig ? $this->never() : $this->once())
            ->method('setConfig')
            ->willReturnCallback(function (array $config) use ($expectedConfig) {
                $this->assertEquals($expectedConfig, $config);
            })
        ;

        return $builderMock;
    }
}

class SampleWithoutConstraintFilters extends Filters
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
    public function buildFilters(?Filters $filters = null)
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
            || SampleWithoutConstraintFilters::class === $filterClassName
        ;
    }
}
