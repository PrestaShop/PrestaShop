<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Resolves the shop-availability inputs, then delegates the actual filtering to
 * ExtraPropertyDefinitionCollection::filterByShops().
 *
 * The module→shops association (fallback for module-owned definitions without an explicit
 * restriction) is cached in the shared extra-property filesystem pool with PER-SHOP keys:
 *  - every module state change (install, enable, disable, …) dispatches a
 *    ModuleManagementEvent whose subscriber runs SymfonyCacheClearer, which wipes
 *    var/cache/{env} — the pool's directory — so module actions invalidate these entries
 *    without any coupling from this class;
 *  - a shop created or duplicated after the cache was warmed simply has no entry yet,
 *    so per-shop keys can never serve stale data for it.
 *
 * The multistore flag and both association lookups are read with direct queries, so the
 * service is constructible in every container the pool and resolver are wired in — the
 * three Symfony kernels and the hand-built FO legacy container (same constraint as
 * ShopListResolver::getDefaultShopId()).
 */
class ExtraPropertyDefinitionShopFilter implements ExtraPropertyDefinitionShopFilterInterface
{
    /**
     * Request-level memoization on top of the filesystem pool.
     *
     * @var array<int, list<string>>
     */
    protected array $moduleNamesByShop = [];

    /**
     * @var list<string>|null
     */
    protected ?array $associatedModuleNames = null;

    protected ?bool $multiShopActive = null;

    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $prefix,
        protected readonly ShopListResolverInterface $shopListResolver,
        protected readonly CacheInterface $definitionCache,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function filterByShopConstraint(
        ExtraPropertyDefinitionCollection $definitions,
        ShopConstraint $shopConstraint,
    ): ExtraPropertyDefinitionCollection {
        if ($definitions->isEmpty() || !$this->isMultiShopActive()) {
            return $definitions;
        }

        return $this->filterByShopIds($definitions, $this->shopListResolver->resolveShopIds($shopConstraint));
    }

    /**
     * {@inheritdoc}
     */
    public function filterByShopIds(
        ExtraPropertyDefinitionCollection $definitions,
        array $shopIds,
    ): ExtraPropertyDefinitionCollection {
        if ($definitions->isEmpty() || !$this->isMultiShopActive()) {
            return $definitions;
        }

        return $definitions->filterByShops($shopIds, $this->getModuleShopIdsByName($definitions, $shopIds));
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableShopIds(ExtraPropertyDefinition $definition, array $shopIds): array
    {
        if (!$this->isMultiShopActive()) {
            return $shopIds;
        }

        // Same rule as ExtraPropertyDefinition::isAvailableForShops(): only a non-empty
        // set restricts ([] is the transient write-time "clear" marker).
        $explicitShopIds = $definition->getAssociatedShopIds();
        if (!empty($explicitShopIds)) {
            return array_values(array_intersect($shopIds, $explicitShopIds));
        }

        $moduleName = $definition->getModuleName();
        if (null === $moduleName || !in_array($moduleName, $this->getAssociatedModuleNames(), true)) {
            // Core-owned, or module with no shop rows at all: unrestricted.
            return $shopIds;
        }

        return array_values(array_filter(
            $shopIds,
            fn (int $shopId): bool => in_array($moduleName, $this->getModuleNamesForShop($shopId), true)
        ));
    }

    /**
     * Builds the module→shops map ExtraPropertyDefinitionCollection::filterByShops() expects,
     * restricted to the modules that actually need it: module-owned definitions without an
     * explicit restriction. Modules with no ps_module_shop row at all are deliberately left
     * OUT of the map (absent key → null → unrestricted, the degenerate rule documented on
     * ExtraPropertyDefinition::isAvailableForShops()); modules with rows get the subset of
     * $shopIds they are enabled on — possibly empty, which means "not available here".
     *
     * @param list<int> $shopIds
     *
     * @return array<string, list<int>>
     */
    protected function getModuleShopIdsByName(ExtraPropertyDefinitionCollection $definitions, array $shopIds): array
    {
        $moduleNames = [];
        foreach ($definitions as $definition) {
            if (null !== $definition->getModuleName() && null === $definition->getAssociatedShopIds()) {
                $moduleNames[$definition->getModuleName()] = true;
            }
        }
        if ([] === $moduleNames) {
            return [];
        }

        $associatedModuleNames = $this->getAssociatedModuleNames();
        $moduleShopIdsByName = [];
        foreach (array_keys($moduleNames) as $moduleName) {
            if (!in_array($moduleName, $associatedModuleNames, true)) {
                continue;
            }
            $moduleShopIdsByName[$moduleName] = array_values(array_filter(
                $shopIds,
                fn (int $shopId): bool => in_array($moduleName, $this->getModuleNamesForShop($shopId), true)
            ));
        }

        return $moduleShopIdsByName;
    }

    /**
     * Module technical names enabled on one shop, cached per shop id.
     *
     * @return list<string>
     */
    protected function getModuleNamesForShop(int $shopId): array
    {
        if (!array_key_exists($shopId, $this->moduleNamesByShop)) {
            $this->moduleNamesByShop[$shopId] = $this->definitionCache->get(
                'extra_property_shop_modules_' . $shopId,
                fn (): array => array_map(
                    strval(...),
                    $this->connection->createQueryBuilder()
                        ->select('m.name')
                        ->from($this->prefix . 'module_shop', 'ms')
                        ->innerJoin('ms', $this->prefix . 'module', 'm', 'm.id_module = ms.id_module')
                        ->where('ms.id_shop = :shopId')
                        ->setParameter('shopId', $shopId)
                        ->fetchFirstColumn()
                )
            );
        }

        return $this->moduleNamesByShop[$shopId];
    }

    /**
     * Module technical names having at least one ps_module_shop row, anywhere.
     *
     * Needed to tell apart "module enabled on other shops only" (definition filtered out)
     * from "module has no shop rows at all" (unrestricted — registration happens during
     * install(), before the module is enabled on any shop).
     *
     * @return list<string>
     */
    protected function getAssociatedModuleNames(): array
    {
        if (null === $this->associatedModuleNames) {
            $this->associatedModuleNames = $this->definitionCache->get(
                'extra_property_associated_modules',
                fn (): array => array_map(
                    strval(...),
                    $this->connection->createQueryBuilder()
                        ->select('DISTINCT m.name')
                        ->from($this->prefix . 'module_shop', 'ms')
                        ->innerJoin('ms', $this->prefix . 'module', 'm', 'm.id_module = ms.id_module')
                        ->fetchFirstColumn()
                )
            );
        }

        return $this->associatedModuleNames;
    }

    /**
     * PS_MULTISHOP_FEATURE_ACTIVE, read with a direct global-configuration query (no
     * configuration service — not available in every container) and memoized per request.
     */
    protected function isMultiShopActive(): bool
    {
        if (null === $this->multiShopActive) {
            $qb = $this->connection->createQueryBuilder()
                ->select('c.value')
                ->from($this->prefix . 'configuration', 'c')
                ->andWhere('c.name = :name')
                ->andWhere('c.id_shop IS NULL OR c.id_shop = 0')
                ->andWhere('c.id_shop_group IS NULL OR c.id_shop_group = 0')
                ->setParameter('name', 'PS_MULTISHOP_FEATURE_ACTIVE')
                ->setMaxResults(1);

            $this->multiShopActive = (bool) $this->connection->fetchOne($qb->getSQL(), $qb->getParameters());
        }

        return $this->multiShopActive;
    }
}
