<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\Exception\UnknownEntityTypeException;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Collects every registered entity importer (tagged services) and indexes them
 * by entity type. Single source of truth for the entity dropdown, the mapping
 * page field list and batch dispatch. Modules can register their own importers
 * by implementing EntityImporterInterface with an autoconfigured service.
 */
final class EntityImporterRegistry
{
    public const SERVICE_TAG = 'core.import.entity_importer';

    /**
     * @var array<string, EntityImporterInterface>|null lazily built index, keyed by entity type
     */
    private ?array $index = null;

    /**
     * @param iterable<EntityImporterInterface> $importers
     */
    public function __construct(
        #[TaggedIterator(self::SERVICE_TAG)]
        private readonly iterable $importers,
    ) {
    }

    public function has(string $entityType): bool
    {
        return isset($this->getIndex()[$entityType]);
    }

    /**
     * @throws UnknownEntityTypeException
     */
    public function get(string $entityType): EntityImporterInterface
    {
        $importer = $this->getIndex()[$entityType] ?? null;
        if (null === $importer) {
            throw new UnknownEntityTypeException(sprintf('No importer is registered for entity type "%s"', $entityType));
        }

        return $importer;
    }

    /**
     * @return array<string, EntityImporterInterface> keyed by entity type
     */
    public function all(): array
    {
        return $this->getIndex();
    }

    /**
     * @return array<string, EntityImporterInterface>
     */
    private function getIndex(): array
    {
        if (null === $this->index) {
            $this->index = [];
            foreach ($this->importers as $importer) {
                $this->index[$importer->getEntityType()] = $importer;
            }
        }

        return $this->index;
    }
}
