<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Api;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Request-scoped store of the extra-property values a grid query already fetched and cast, so a list
 * (collection) response can reuse them instead of re-reading the database row by row.
 *
 * It is populated from QueryListProvider with the records returned by the grid data factory — which the
 * ExtraPropertiesGridQueryBuilderModifier enriched with one column per grid-associated extra property — and read
 * back by ExtraPropertyApiSubscriber while enriching each list item.
 *
 * Registered only in the Admin API kernel. Records are keyed by entity name then entity id; values keep the grid
 * column name (ExtraPropertyDefinition::getFieldName()) and the single context-locale value the grid fetched.
 */
class ExtraPropertyApiListRecordCollector implements ResetInterface
{
    /**
     * @var array<string, array<int, array<string, mixed>>>
     */
    protected array $recordsByEntity = [];

    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
    ) {
    }

    /**
     * Captures, from the given grid records, the extra-property columns of the definitions that target the
     * operation (URI template + HTTP method). A column only ends up stored when the grid actually fetched it
     * (i.e. the property is both API- and grid-associated), so nothing not displayed in a list is kept.
     *
     * @param array<int, array<string, mixed>> $records Records as returned by the grid data factory
     */
    public function capture(array $records, string $uriTemplate, string $method): void
    {
        $definitions = $this->repository->getAllDefinitions()->filterByApi($uriTemplate, $method);
        if ($definitions->isEmpty()) {
            return;
        }

        foreach ($this->groupByEntity($definitions) as $entityName => $entityDefinitions) {
            $primaryKey = $entityDefinitions->first()->getPrimaryKeyName();

            foreach ($records as $record) {
                if (!is_array($record) || !isset($record[$primaryKey])) {
                    continue;
                }

                $entityId = (int) $record[$primaryKey];
                if ($entityId <= 0) {
                    continue;
                }

                foreach ($entityDefinitions as $definition) {
                    $alias = $definition->getFieldName();
                    if (array_key_exists($alias, $record)) {
                        $this->recordsByEntity[$entityName][$entityId][$alias] = $record[$alias];
                    }
                }
            }
        }
    }

    /**
     * @return array<string, mixed>|null Extra-property columns (formFieldName => value) captured for the row
     */
    public function find(string $entityName, int $entityId): ?array
    {
        return $this->recordsByEntity[$entityName][$entityId] ?? null;
    }

    public function reset(): void
    {
        $this->recordsByEntity = [];
    }

    /**
     * @return array<string, ExtraPropertyDefinitionCollection>
     */
    protected function groupByEntity(ExtraPropertyDefinitionCollection $definitions): array
    {
        $byEntity = [];
        foreach ($definitions as $definition) {
            $byEntity[$definition->getEntityName()][] = $definition;
        }

        return array_map(
            static fn (array $defs): ExtraPropertyDefinitionCollection => new ExtraPropertyDefinitionCollection($defs),
            $byEntity
        );
    }
}
