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

/**
 * This class require Xcache extension.
 *
 * @since 1.5.0
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
