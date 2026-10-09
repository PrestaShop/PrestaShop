<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use Cache;

/**
 * Stores the cached storefront policy in PrestaShop's configured cache backend (the legacy Cache, the same
 * one the storefront uses), which — unlike a Symfony cache pool — is available in the hand-built
 * front-office container that serves the legacy dispatch. See {@see CspPolicyCacheInterface}.
 */
final class CspPolicyCache implements CspPolicyCacheInterface
{
    public function get(int $shopId): ?array
    {
        $cache = Cache::getInstance();
        $key = $this->key($shopId);
        if (!$cache->exists($key)) {
            return null;
        }

        $directives = $cache->get($key);

        return is_array($directives) ? $directives : null;
    }

    public function store(int $shopId, array $directives): void
    {
        Cache::getInstance()->set($this->key($shopId), $directives);
    }

    public function invalidate(int $shopId): void
    {
        Cache::getInstance()->delete($this->key($shopId));
    }

    private function key(int $shopId): string
    {
        return 'csp_policy_front_shop_' . $shopId;
    }
}
