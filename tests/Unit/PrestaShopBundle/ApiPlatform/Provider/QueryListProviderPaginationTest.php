<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Builder\FiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSPaginate;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class QueryListProviderPaginationTest extends TestCase
{
    /**
     * @dataProvider getInvalidPaginationParameters
     */
    public function testInvalidPaginationIsRejectedBeforeTheQuery(array $filters, string $expectedMessage): void
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->expects($this->never())->method('handle');

        $provider = $this->createProvider($queryBus);
        $operation = new CQRSPaginate(
            class: stdClass::class,
            CQRSQuery: stdClass::class,
        );

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage($expectedMessage);

        $provider->provide($operation, [], ['filters' => $filters]);
    }

    public static function getInvalidPaginationParameters(): iterable
    {
        yield 'negative offset' => [['offset' => -1], 'Offset must be an integer greater than or equal to zero, -1 given.'];
        yield 'negative offset string' => [['offset' => '-4'], 'Offset must be an integer greater than or equal to zero, -4 given.'];
        yield 'offset array' => [['offset' => ['1']], 'Offset must be an integer greater than or equal to zero, array given.'];
        yield 'offset empty string' => [['offset' => ''], 'Offset must be an integer greater than or equal to zero, \'\' given.'];
        yield 'offset text' => [['offset' => 'abc'], 'Offset must be an integer greater than or equal to zero, abc given.'];
        yield 'offset float string' => [['offset' => '1.9'], 'Offset must be an integer greater than or equal to zero, 1.9 given.'];
        yield 'offset scientific notation' => [['offset' => '1e1'], 'Offset must be an integer greater than or equal to zero, 1e1 given.'];
        yield 'offset leading zeros' => [['offset' => '010'], 'Offset must be an integer greater than or equal to zero, 010 given.'];
        yield 'zero limit' => [['limit' => 0], sprintf('Limit must be an integer between 1 and %d, 0 given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'zero limit string' => [['limit' => '0'], sprintf('Limit must be an integer between 1 and %d, 0 given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'negative limit' => [['limit' => -1], sprintf('Limit must be an integer between 1 and %d, -1 given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit array' => [['limit' => ['10']], sprintf('Limit must be an integer between 1 and %d, array given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit empty string' => [['limit' => ''], sprintf('Limit must be an integer between 1 and %d, \'\' given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit text' => [['limit' => 'abc'], sprintf('Limit must be an integer between 1 and %d, abc given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit float string' => [['limit' => '1.9'], sprintf('Limit must be an integer between 1 and %d, 1.9 given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit scientific notation' => [['limit' => '1e1'], sprintf('Limit must be an integer between 1 and %d, 1e1 given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit trailing garbage' => [['limit' => '10abc'], sprintf('Limit must be an integer between 1 and %d, 10abc given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit leading zeros' => [['limit' => '010'], sprintf('Limit must be an integer between 1 and %d, 010 given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
        yield 'limit above the maximum' => [['limit' => 100000000], sprintf('Limit must be an integer between 1 and %d, 100000000 given.', QueryListProvider::MAX_PAGINATED_ITEM_LIMIT)];
    }

    /**
     * @dataProvider getValidPaginationParameters
     */
    public function testValidPaginationIsForwarded(array $filters, ?int $expectedOffset, int $expectedLimit, bool $builderHasLimit): void
    {
        $provider = $this->createProvider($this->createMock(CommandBusInterface::class), $builderHasLimit);
        $operation = new CQRSPaginate(class: stdClass::class);

        $method = new ReflectionMethod(QueryListProvider::class, 'createFilters');
        /** @var Filters $built */
        $built = $method->invoke($provider, ['filters' => $filters], $operation);

        $this->assertSame($expectedOffset, $built->getOffset());
        $this->assertSame($expectedLimit, $built->getLimit());
    }

    public static function getValidPaginationParameters(): iterable
    {
        yield 'omitted parameters use the provider default' => [[], null, QueryListProvider::DEFAULT_PAGINATED_ITEM_LIMIT, false];
        // Filters::getOffset() stores 0 as null; PaginationElements turns that back into 0.
        // builderHasLimit stays false: RequestFiltersBuilder copies the query through ParameterBag::getInt,
        // which is null for an empty request (and for values FILTER_VALIDATE_INT rejects). The checked client
        // limit must still be kept, not replaced by the default of 50.
        yield 'offset zero and positive limit' => [['offset' => 0, 'limit' => 10], null, 10, false];
        yield 'positive offset and limit' => [['offset' => 20, 'limit' => 5], 20, 5, false];
        yield 'numeric strings' => [['offset' => '15', 'limit' => '25'], 15, 25, false];
        yield 'maximum limit' => [['limit' => QueryListProvider::MAX_PAGINATED_ITEM_LIMIT], null, QueryListProvider::MAX_PAGINATED_ITEM_LIMIT, false];
    }

    public function testCheckedPaginationIsPassedToTheCqrsQuery(): void
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->expects($this->once())->method('handle')->willReturn(new stdClass());

        $serializer = $this->createMock(CQRSApiSerializer::class);
        $serializer->method('normalize')->willReturnCallback(
            static fn (mixed $data) => $data instanceof stdClass ? ['items' => [], 'count' => 0] : $data
        );
        $denormalized = null;
        $serializer->method('denormalize')->willReturnCallback(
            static function (mixed $data, string $type) use (&$denormalized) {
                if (stdClass::class === $type && is_array($data)) {
                    $denormalized = $data;
                }

                return new stdClass();
            }
        );

        $provider = $this->createProvider($queryBus, false, $serializer);
        $operation = new CQRSPaginate(
            class: stdClass::class,
            CQRSQuery: stdClass::class,
        );

        $provider->provide($operation, [], ['filters' => ['offset' => '0', 'limit' => '10']]);

        $this->assertIsArray($denormalized);
        $this->assertSame(0, $denormalized['offset']);
        $this->assertSame(10, $denormalized['limit']);
    }

    private function createProvider(CommandBusInterface $queryBus, bool $builderHasLimit = false, ?CQRSApiSerializer $serializer = null): QueryListProvider
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        if (null === $serializer) {
            $serializer = $this->createMock(CQRSApiSerializer::class);
            $serializer->method('normalize')->willReturnArgument(0);
        }

        $filtersBuilder = $this->createMock(FiltersBuilderInterface::class);
        $filtersBuilder->method('setConfig')->willReturnSelf();
        $filtersBuilder->method('buildFilters')->willReturnCallback(
            static fn () => new Filters($builderHasLimit ? ['limit' => 1] : [])
        );

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(1));

        $contextParameters = $this->createMock(ContextParametersProvider::class);
        $contextParameters->method('getContextParameters')->willReturn([]);

        return new QueryListProvider(
            $requestStack,
            $serializer,
            $this->createMock(ContainerInterface::class),
            $shopContext,
            $filtersBuilder,
            $queryBus,
            $contextParameters,
            $this->createMock(PropertyAccessorInterface::class),
        );
    }
}
