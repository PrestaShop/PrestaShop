<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

/**
 * Caches the shop-stable part of the storefront CSP policy (the base plus the shop's curated rules and the
 * module hook contributions) so a front page does not re-query the allow-list and re-dispatch the hook on
 * every request. The active theme's contributions are not cached: the provider merges them on top each
 * time, so a theme change needs no invalidation. The rule-mutation handlers (allow, add, revoke, bulk
 * revoke) invalidate the shop's entry; module operations clear the whole cache backend this uses, which is
 * why hook contributions must be stable for a shop, not per request.
 */
interface CspPolicyCacheInterface
{
    /**
     * @return array<string, list<string>>|null the cached directives, or null on a miss
     */
    public function get(int $shopId): ?array;

    /**
     * @param array<string, list<string>> $directives
     */
    public function store(int $shopId, array $directives): void;

    public function invalidate(int $shopId): void;
}
