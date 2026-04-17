<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache;

use Cache;

/**
 * Adapter for generic cache methods.
 */
class CacheAdapter
{
    /**
     * @param string $key
     * @param string $value
     */
    public function store($key, $value)
    {
        return Cache::store($key, $value);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function retrieve($key)
    {
        return Cache::retrieve($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isStored($key)
    {
        return Cache::isStored($key);
    }
}
