<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Shop;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Orchestration layer over ShopRepository, which owns the single constraint → shop ids
 * query (usable shops only for group/all scopes): this service adds the per-request
 * memoization and the representative-shop rule. Usable in every container the repository
 * is wired in (the three Symfony kernels and the hand-built FO legacy container).
 *
 * Group and all-shops lookups are memoized per request — a request that creates or
 * deletes a shop and resolves the same scope again reads the memoized list, which is
 * acceptable for the fan-out/read use cases this serves.
 */
class ShopListResolver implements ShopListResolverInterface
{
    /**
     * @var array<string, list<int>>
     */
    protected array $shopIdsCache = [];

    protected ?int $defaultShopId = null;

    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $prefix,
        protected readonly ShopRepository $shopRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function resolveShopIds(ShopConstraint $shopConstraint): array
    {
        // Explicit ids need no query and no memoization.
        if (null !== $shopConstraint->getShopId()) {
            return [$shopConstraint->getShopId()->getValue()];
        }

        if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            return array_map(static fn (ShopId $shopId): int => $shopId->getValue(), $shopConstraint->getShopIds());
        }

        $groupId = $shopConstraint->getShopGroupId()?->getValue();
        $cacheKey = null === $groupId ? 'all' : 'group_' . $groupId;
        if (!array_key_exists($cacheKey, $this->shopIdsCache)) {
            $this->shopIdsCache[$cacheKey] = $this->shopRepository->getAssociatedShopIds($shopConstraint);
        }

        return $this->shopIdsCache[$cacheKey];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRepresentativeShopId(ShopConstraint $shopConstraint): int
    {
        if (null !== $shopConstraint->getShopId()) {
            return $shopConstraint->getShopId()->getValue();
        }

        $shopIds = $this->resolveShopIds($shopConstraint);
        if ([] === $shopIds) {
            return 0;
        }

        $defaultShopId = $this->getDefaultShopId();
        if (in_array($defaultShopId, $shopIds, true)) {
            return $defaultShopId;
        }

        return min($shopIds);
    }

    /**
     * PS_SHOP_DEFAULT is a global-only configuration value, read with a direct query so
     * this service has no dependency on a configuration service (which is not available
     * in every container). Global rows use NULL shop/group columns (0 on legacy installs).
     */
    protected function getDefaultShopId(): int
    {
        if (null === $this->defaultShopId) {
            $qb = $this->connection->createQueryBuilder()
                ->select('c.value')
                ->from($this->prefix . 'configuration', 'c')
                ->andWhere('c.name = :name')
                ->andWhere('c.id_shop IS NULL OR c.id_shop = 0')
                ->andWhere('c.id_shop_group IS NULL OR c.id_shop_group = 0')
                ->setParameter('name', 'PS_SHOP_DEFAULT')
                ->setMaxResults(1);

            $this->defaultShopId = (int) $this->connection->fetchOne($qb->getSQL(), $qb->getParameters());
        }

        return $this->defaultShopId;
    }
}
