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
        yield 'negative offset' => [['offset' => -1], 'Offset must be a positive integer or zero, -1 given.'];
        yield 'negative offset string' => [['offset' => '-4'], 'Offset must be a positive integer or zero, -4 given.'];
        yield 'zero limit' => [['limit' => 0], 'Limit must be a positive integer, 0 given.'];
        yield 'zero limit string' => [['limit' => '0'], 'Limit must be a positive integer, 0 given.'];
        yield 'negative limit' => [['limit' => -1], 'Limit must be a positive integer, -1 given.'];
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
        // A builder that already resolved a limit must not have the client limit replaced by the default of 50.
        yield 'offset zero and positive limit' => [['offset' => 0, 'limit' => 10], null, 10, true];
        yield 'positive offset and limit' => [['offset' => 20, 'limit' => 5], 20, 5, true];
        yield 'numeric strings' => [['offset' => '15', 'limit' => '25'], 15, 25, true];
    }

    private function createProvider(CommandBusInterface $queryBus, bool $builderHasLimit = false): QueryListProvider
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        $serializer = $this->createMock(CQRSApiSerializer::class);
        $serializer->method('normalize')->willReturnArgument(0);

        $filtersBuilder = $this->createMock(FiltersBuilderInterface::class);
        $filtersBuilder->method('setConfig')->willReturnSelf();
        $filtersBuilder->method('buildFilters')->willReturnCallback(
            static fn () => new Filters($builderHasLimit ? ['limit' => 1] : [])
        );

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(1));

        return new QueryListProvider(
            $requestStack,
            $serializer,
            $this->createMock(ContainerInterface::class),
            $shopContext,
            $filtersBuilder,
            $queryBus,
            $this->createMock(ContextParametersProvider::class),
            $this->createMock(PropertyAccessorInterface::class),
        );
    }
}
