<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use Symfony\Component\Cache\Exception\LogicException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Cache-decorating repository: wraps ExtraPropertyDefinitionRepository for both reads and writes.
 *
 * Read: caches getAllDefinitions() under a single global key; findDefinitionByModuleAndField()
 * is never cached (write-path lookup that must always reflect current DB state).
 *
 * Write: delegates to the inner repository and invalidates the cache after each successful write.
 * This is the single point of cache invalidation for extra property definitions.
 *
 * Cache key scheme: "extra_property_definition_all" (one entry for all definitions).
 * Cache tags: ["extra_property_definition"] (when tag-aware).
 */
class CachedExtraPropertyDefinitionRepository implements ExtraPropertyDefinitionRepositoryInterface, ExtraPropertyDefinitionWriterInterface
{
    public const CACHE_KEY = 'extra_property_definition_all';

    public function __construct(
        protected readonly ExtraPropertyDefinitionRepository $repository,
        protected readonly CacheInterface $definitionCache,
    ) {
    }

    // -------------------------------------------------------------------------
    // Read — ExtraPropertyDefinitionRepositoryInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Cached under a single global key. All callers use collection filter helpers
     * (filterByEntity, filterByForm, filterByGrid, etc.) to narrow the result.
     */
    public function getAllDefinitions(): ExtraPropertyDefinitionCollection
    {
        return $this->definitionCache->get(self::CACHE_KEY, function (ItemInterface $item): ExtraPropertyDefinitionCollection {
            try {
                $item->tag(['extra_property_definition']);
            } catch (LogicException) {
                // Pool may not be tag-aware; key-based invalidation still works.
            }

            return $this->repository->getAllDefinitions();
        });
    }

    /**
     * {@inheritdoc}
     *
     * Not cached: targeted write-path lookup that must always reflect current DB state.
     */
    public function findDefinitionByModuleAndField(string $entityName, ?string $moduleName, string $fieldName): ?ExtraPropertyDefinition
    {
        return $this->repository->findDefinitionByModuleAndField($entityName, $moduleName, $fieldName);
    }

    /**
     * {@inheritdoc}
     *
     * Not cached: same rationale as findDefinitionByModuleAndField().
     */
    public function getDefinitionById(int $id): ?ExtraPropertyDefinition
    {
        return $this->repository->getDefinitionById($id);
    }

    /**
     * {@inheritdoc}
     *
     * Not cached: same rationale as findDefinitionByModuleAndField().
     */
    public function getUnprotectedDefinitionById(int $id): ExtraPropertyDefinition
    {
        return $this->repository->getUnprotectedDefinitionById($id);
    }

    // -------------------------------------------------------------------------
    // Write — ExtraPropertyDefinitionWriterInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function save(ExtraPropertyDefinition $definition): int|false
    {
        $result = $this->repository->save($definition);
        if (false !== $result) {
            $this->invalidateCache();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $result = $this->repository->delete($id);
        if ($result) {
            $this->invalidateCache();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByDefinition(ExtraPropertyDefinition $definition): bool
    {
        $result = $this->repository->deleteByDefinition($definition);
        if ($result) {
            $this->invalidateCache();
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Cache management
    // -------------------------------------------------------------------------

    /**
     * Removes the global definition cache entry so the next getAllDefinitions() reloads from DB.
     */
    protected function invalidateCache(): void
    {
        $this->definitionCache->delete(self::CACHE_KEY);
    }
}
