<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

/**
 * A per-shop copy of the storefront allow-list kept as a configuration value, so the policy is built
 * without an allow-list query per page (config loads once per request; it lives in the shop's DB, so it is
 * unique per install and shared across servers). Only the rules are cached — base, theme and the hook run
 * every request, so module contributions never go stale. csp_rule stays the source of truth.
 */
interface CspRulesSnapshotInterface
{
    /**
     * The shop's storefront allow-list as directive => sources, rebuilding and storing it on a miss.
     *
     * @return array<string, list<string>>
     */
    public function get(int $shopId): array;

    /** Rebuilds the shop's snapshot from csp_rule and stores it. */
    public function refresh(int $shopId): void;
}
