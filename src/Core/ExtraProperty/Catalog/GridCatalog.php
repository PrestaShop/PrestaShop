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
use Symfony\Contracts\Service\ServiceProviderInterface;
use Throwable;

/**
 * Enumerates the back-office grids by iterating every service tagged core.grid_definition_factory
 * (the tag is applied to all GridDefinitionFactoryInterface implementations via _instanceof, so
 * module-provided factories are included). Factories whose definition cannot be built are logged
 * and skipped, so one broken grid never breaks the whole catalog.
 *
 * The scan is memoized per instance; a cache decorator can later wrap this service for
 * cross-request caching (see the prestashop.extra_property.catalog.filesystem_cache pool).
 */
final class GridCatalog implements GridCatalogInterface
{
    /**
     * @var array<string, GridCatalogEntry>|null indexed by grid id, sorted by label
     */
    private ?array $entries = null;

    public function __construct(
        #[AutowireLocator('core.grid_definition_factory')]
        private readonly ServiceProviderInterface $gridDefinitionFactories,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getAll(): array
    {
        return array_values($this->getEntries());
    }

    public function get(string $gridId): ?GridCatalogEntry
    {
        return $this->getEntries()[$gridId] ?? null;
    }

    public function has(string $gridId): bool
    {
        return isset($this->getEntries()[$gridId]);
    }

    /**
     * @return array<string, GridCatalogEntry>
     */
    private function getEntries(): array
    {
        if (null !== $this->entries) {
            return $this->entries;
        }

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
                    $columns[] = new GridColumnEntry(
                        (string) $column->getId(),
                        (string) $column->getName(),
                        $position++,
                    );
                }

                $gridId = (string) $definition->getId();
                $entries[$gridId] = new GridCatalogEntry($gridId, (string) $definition->getName(), $columns);
            } catch (Throwable $e) {
                $this->logger->warning(
                    sprintf('Extra property grid catalog: skipped grid definition factory "%s": %s', $serviceId, $e->getMessage()),
                    ['exception' => $e],
                );
            }
        }

        uasort($entries, static fn (GridCatalogEntry $a, GridCatalogEntry $b): int => strcasecmp($a->label, $b->label));

        return $this->entries = $entries;
    }
}
