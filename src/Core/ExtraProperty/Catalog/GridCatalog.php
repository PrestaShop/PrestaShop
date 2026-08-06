<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Throwable;

/**
 * Enumerates the back-office grids an extra property definition can be associated with, by
 * iterating every service tagged core.grid_definition_factory (the tag is applied to all
 * GridDefinitionFactoryInterface implementations via _instanceof, so module-provided factories
 * are included). Factories whose definition cannot be built are logged and skipped, so one
 * broken grid never breaks the whole catalog.
 *
 * The scan is memoized per instance and cached cross-request in the
 * prestashop.extra_property.catalog.filesystem_cache pool — the grids are bound to the deployed
 * code and installed modules, whose management already clears the Symfony cache the pool lives
 * in, so no dedicated invalidation is needed.
 *
 * @phpstan-type GridEntry array{id: string, label: string, columns: list<array{id: string, label: string, position: int}>}
 */
class GridCatalog
{
    private const CACHE_KEY = 'grids';

    /**
     * @var array<string, GridEntry>|null indexed by grid id, sorted by label
     */
    private ?array $entries = null;

    public function __construct(
        #[AutowireLocator('core.grid_definition_factory')]
        private readonly ServiceProviderInterface $gridDefinitionFactories,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @return list<GridEntry> sorted by label
     */
    public function getAll(): array
    {
        return array_values($this->getEntries());
    }

    /**
     * @return GridEntry|null
     */
    public function get(string $gridId): ?array
    {
        return $this->getEntries()[$gridId] ?? null;
    }

    public function has(string $gridId): bool
    {
        return isset($this->getEntries()[$gridId]);
    }

    /**
     * @return array<string, GridEntry>
     */
    private function getEntries(): array
    {
        return $this->entries ??= $this->cache->get(self::CACHE_KEY, fn (): array => $this->scanEntries());
    }

    /**
     * @return array<string, GridEntry>
     */
    private function scanEntries(): array
    {
        $entries = [];
        foreach (array_keys($this->gridDefinitionFactories->getProvidedServices()) as $serviceId) {
            try {
                $factory = $this->gridDefinitionFactories->get($serviceId);
                if (!$factory instanceof GridDefinitionFactoryInterface) {
                    continue;
                }

                $definition = $factory->getDefinition();

                $columns = [];
                $position = 0;
                foreach ($definition->getColumns() as $column) {
                    $columns[] = [
                        'id' => (string) $column->getId(),
                        'label' => (string) $column->getName(),
                        'position' => $position++,
                    ];
                }

                $gridId = (string) $definition->getId();
                $entries[$gridId] = [
                    'id' => $gridId,
                    'label' => (string) $definition->getName(),
                    'columns' => $columns,
                ];
            } catch (Throwable $e) {
                $this->logger->warning(
                    sprintf('Extra property grid catalog: skipped grid definition factory "%s": %s', $serviceId, $e->getMessage()),
                    ['exception' => $e],
                );
            }
        }

        uasort($entries, static fn (array $a, array $b): int => strcasecmp($a['label'], $b['label']));

        return $entries;
    }
}
