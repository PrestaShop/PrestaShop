<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Shop;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * DBAL implementation, usable in every container (the three Symfony kernels and the
 * hand-built FO legacy container): it only needs the DBAL connection, no other service.
 *
 * Group and all-shops lookups are memoized per request; the memo key includes the
 * constraint shape so distinct constraints never share an entry.
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
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function resolveShopIds(ShopConstraint $shopConstraint): array
    {
        if (null !== $shopConstraint->getShopId()) {
            return [$shopConstraint->getShopId()->getValue()];
        }

        if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            return array_map(static fn (ShopId $shopId): int => $shopId->getValue(), $shopConstraint->getShopIds());
        }

        $groupId = $shopConstraint->getShopGroupId()?->getValue();
        $cacheKey = null === $groupId ? 'all' : 'group_' . $groupId;
        if (!array_key_exists($cacheKey, $this->shopIdsCache)) {
            $sql = 'SELECT id_shop FROM ' . $this->connection->quoteIdentifier($this->prefix . 'shop');
            $params = [];
            if (null !== $groupId) {
                $sql .= ' WHERE id_shop_group = ?';
                $params[] = $groupId;
            }
            $sql .= ' ORDER BY id_shop ASC';

            $this->shopIdsCache[$cacheKey] = array_map(
                static fn (array $row): int => (int) $row['id_shop'],
                $this->connection->fetchAllAssociative($sql, $params)
            );
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
     * PS_SHOP_DEFAULT is a global-only configuration value, read with plain SQL so this
     * service has no dependency on a configuration service (which is not available in
     * every container). Global rows use NULL shop/group columns (0 on legacy installs).
     */
    protected function getDefaultShopId(): int
    {
        if (null === $this->defaultShopId) {
            $sql = 'SELECT value FROM ' . $this->connection->quoteIdentifier($this->prefix . 'configuration')
                . ' WHERE name = ? AND (id_shop IS NULL OR id_shop = 0) AND (id_shop_group IS NULL OR id_shop_group = 0)';

            $this->defaultShopId = (int) $this->connection->fetchOne($sql, ['PS_SHOP_DEFAULT']);
        }

        return $this->defaultShopId;
    }
}
