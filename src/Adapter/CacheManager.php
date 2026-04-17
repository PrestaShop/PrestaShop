<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Cache;

/**
 * Class CacheManager drives the cache behavior.
 *
 * Features to drive the legacy cache from new code architecture.
 */
class CacheManager
{
    /**
     * Cleans the cache for specific cache key.
     *
     * @param string $key
     */
    public function clean($key)
    {
        Cache::clean($key);
    }
}
