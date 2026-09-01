<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Shop;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
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

    public function __construct(
        protected readonly ShopRepository $shopRepository,
        protected readonly ShopConfigurationInterface $configuration,
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
     * PS_SHOP_DEFAULT is a global-only configuration value, hence the explicit all-shops
     * constraint (same pattern as the shop context listeners).
     */
    protected function getDefaultShopId(): int
    {
        return (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops());
    }
}
