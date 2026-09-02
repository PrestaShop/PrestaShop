<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Converter;

/**
 * Interface CacheKeyGeneratorInterface is used by CacheProvider to generate
 * the key used for its cache, it allows to update the cache easily by varying the key.
 */
interface CacheKeyGeneratorInterface
{
    /**
     * Returns a string used as key for caching the legacy routes information.
     * You can vary this cache key in order to update the cache when needed.
     * (e.g: RoutingCacheKeyGenerator generates its key based on the last modification
     * date of routing files so that each modifications regenerate the cache).
     *
     * @return string
     */
    public function getCacheKey();
}
