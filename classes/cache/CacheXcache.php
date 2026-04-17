<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * This class require Xcache extension.
 */
class CacheXcacheCore extends Cache
{
    public function __construct()
    {
        $this->keys = xcache_get(self::KEYS_NAME);
        if (!is_array($this->keys)) {
            $this->keys = [];
        }
    }

    /**
     * @see Cache::_set()
     */
    protected function _set($key, $value, $ttl = 0)
    {
        $result = xcache_set($key, $value, $ttl);

        if ($result === false) {
            $this->setAdjustTableCacheSize(true);
        }

        return $result;
    }

    /**
     * @see Cache::_get()
     */
    protected function _get($key)
    {
        return xcache_isset($key) ? xcache_get($key) : false;
    }

    /**
     * @see Cache::_exists()
     */
    protected function _exists($key)
    {
        return xcache_isset($key);
    }

    /**
     * @see Cache::_delete()
     */
    protected function _delete($key)
    {
        return xcache_unset($key);
    }

    /**
     * @see Cache::_writeKeys()
     */
    protected function _writeKeys()
    {
        xcache_set(self::KEYS_NAME, $this->keys);
    }

    /**
     * @see Cache::flush()
     */
    public function flush()
    {
        $this->delete('*');

        return true;
    }
}
