<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Scopes;

use Psr\Cache\CacheException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * This class decorates ApiResourceScopesExtractor and stores the returned value in a filesystem
 * cache, we additionally keep the result in a local class field to avoid too many request on
 * the cache and multiple un-serialization.
 *
 * @internal
 */
class CachedApiResourceScopesExtractor implements ApiResourceScopesExtractorInterface
{
    /**
     * Locale array cache to avoid passing through the cache pool several times in the same request.
     *
     * @var array<string, ApiResourceScopes[]>
     */
    private array $localeCache;

    public function __construct(
        private readonly CacheItemPoolInterface $cacheItemPool,
        private readonly ApiResourceScopesExtractorInterface $decorated
    ) {
    }

    public function getAllApiResourceScopes(): array
    {
        return $this->getCachedResourceOrUseFallback(
            md5(self::class) . 'all_resources',
            function () {
                return $this->decorated->getAllApiResourceScopes();
            }
        );
    }

    public function getEnabledApiResourceScopes(): array
    {
        return $this->getCachedResourceOrUseFallback(
            md5(self::class) . 'enabled_resources',
            function () {
                return $this->decorated->getEnabledApiResourceScopes();
            }
        );
    }

    private function getCachedResourceOrUseFallback(string $cacheKey, callable $fallback): array
    {
        if (isset($this->localeCache[$cacheKey])) {
            return $this->localeCache[$cacheKey];
        }

        try {
            $cachedItem = $this->cacheItemPool->getItem($cacheKey);
            if ($cachedItem->isHit()) {
                $this->localeCache[$cacheKey] = $cachedItem->get();

                return $this->localeCache[$cacheKey];
            }
        } catch (CacheException) {
            // If something went wrong with the cache system we don't cache anything and just return the fallback return value
            return $fallback();
        }

        // Cache is available but was not hit, so we save it both locally and in the pool
        $this->localeCache[$cacheKey] = $fallback();
        $cachedItem->set($this->localeCache[$cacheKey]);
        $this->cacheItemPool->save($cachedItem);

        return $this->localeCache[$cacheKey];
    }
}
