<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\GridCatalog;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Service\ServiceProviderInterface;

class GridCatalogTest extends TestCase
{
    public function testGetAllSkipsBrokenFactoryAndSortsByLabel(): void
    {
        $catalog = new GridCatalog($this->createProvider([
            'grid.factory.zone' => $this->createFactory('zone', 'Zones', ['id_zone' => 'ID', 'name' => 'Zone name']),
            'grid.factory.broken' => $this->createBrokenFactory(),
            'grid.factory.customer' => $this->createFactory('customer', 'Customers', ['id_customer' => 'ID']),
        ]), new NullLogger(), new ArrayAdapter());

        $entries = $catalog->getAll();

        $this->assertCount(2, $entries);
        // Sorted by label: Customers before Zones
        $this->assertSame('customer', $entries[0]['id']);
        $this->assertSame('Customers', $entries[0]['label']);
        $this->assertSame('zone', $entries[1]['id']);
        $this->assertSame('Zones', $entries[1]['label']);

        $this->assertCount(2, $entries[1]['columns']);
        $this->assertSame('id_zone', $entries[1]['columns'][0]['id']);
        $this->assertSame('ID', $entries[1]['columns'][0]['label']);
        $this->assertSame(0, $entries[1]['columns'][0]['position']);
        $this->assertSame('name', $entries[1]['columns'][1]['id']);
        $this->assertSame('Zone name', $entries[1]['columns'][1]['label']);
        $this->assertSame(1, $entries[1]['columns'][1]['position']);
    }

    public function testGetAndHas(): void
    {
        $catalog = new GridCatalog($this->createProvider([
            'grid.factory.zone' => $this->createFactory('zone', 'Zones', ['id_zone' => 'ID']),
            'grid.factory.broken' => $this->createBrokenFactory(),
        ]), new NullLogger(), new ArrayAdapter());

        $this->assertTrue($catalog->has('zone'));
        $this->assertFalse($catalog->has('unknown'));

        $entry = $catalog->get('zone');
        $this->assertNotNull($entry);
        $this->assertSame('Zones', $entry['label']);

        $this->assertNull($catalog->get('unknown'));
    }

    public function testScanIsMemoized(): void
    {
        $definition = $this->createDefinition('zone', 'Zones', ['id_zone' => 'ID']);
        $factory = $this->createMock(GridDefinitionFactoryInterface::class);
        $factory->expects($this->once())->method('getDefinition')->willReturn($definition);

        $cache = new ArrayAdapter();
        $catalog = new GridCatalog($this->createProvider(['grid.factory.zone' => $factory]), new NullLogger(), $cache);

        $catalog->getAll();
        $catalog->getAll();
        $this->assertTrue($catalog->has('zone'));

        // A second instance sharing the pool never scans: the cache is cross-request.
        $brokenProvider = $this->createProvider([]);
        $cachedCatalog = new GridCatalog($brokenProvider, new NullLogger(), $cache);
        $this->assertTrue($cachedCatalog->has('zone'));
    }

    /**
     * @param array<string, GridDefinitionFactoryInterface> $factories
     */
    private function createProvider(array $factories): ServiceProviderInterface
    {
        return new class($factories) implements ServiceProviderInterface {
            public function __construct(private readonly array $factories)
            {
            }

            public function get(string $id): mixed
            {
                if (!isset($this->factories[$id])) {
                    throw new RuntimeException(sprintf('Service "%s" not found', $id));
                }

                return $this->factories[$id];
            }

            public function has(string $id): bool
            {
                return isset($this->factories[$id]);
            }

            public function getProvidedServices(): array
            {
                return array_map(static fn (): string => '?', $this->factories);
            }
        };
    }

    /**
     * @param array<string, string> $columns column id => column name
     */
    private function createFactory(string $gridId, string $gridName, array $columns): GridDefinitionFactoryInterface
    {
        $factory = $this->createMock(GridDefinitionFactoryInterface::class);
        $factory->method('getDefinition')->willReturn($this->createDefinition($gridId, $gridName, $columns));

        return $factory;
    }

    private function createBrokenFactory(): GridDefinitionFactoryInterface
    {
        $factory = $this->createMock(GridDefinitionFactoryInterface::class);
        $factory->method('getDefinition')->willThrowException(new RuntimeException('This factory is broken'));

        return $factory;
    }

    /**
     * @param array<string, string> $columns column id => column name
     */
    private function createDefinition(string $gridId, string $gridName, array $columns): GridDefinitionInterface
    {
        $columnCollection = new ColumnCollection();
        foreach ($columns as $columnId => $columnName) {
            $columnCollection->add((new DataColumn($columnId))->setName($columnName));
        }

        $definition = $this->createMock(GridDefinitionInterface::class);
        $definition->method('getId')->willReturn($gridId);
        $definition->method('getName')->willReturn($gridName);
        $definition->method('getColumns')->willReturn($columnCollection);

        return $definition;
    }
}
