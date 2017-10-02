<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Currency\Repository\Installed;

use Psr\Cache\CacheItemPoolInterface;

class PhpArrayCache implements CacheItemPoolInterface
{

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return \Psr\Cache\CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *   An indexed array of keys of items to retrieve.
     *
     * @return array|\Traversable If any of the keys in $keys are not a legal value a
     *                            \Psr\Cache\InvalidArgumentException If any of the keys in $keys are not a legal value
     *                            a \Psr\Cache\InvalidArgumentException MUST be thrown.
     * @throws \Exception
     */
    public function getItems(array $keys = array())
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *   The key for which to check existence.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        $this->data = array();

        return true;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);

            return true;
        }

        return false;
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool.
     *
     * @return bool If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     * If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     * MUST be thrown.
     * @throws \Exception
     */
    public function deleteItems(array $keys)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Persists a cache item immediately.
     *
     * @param \Psr\Cache\CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(\Psr\Cache\CacheItemInterface $item)
    {
        $this->data[$item->getKey()] = $item->get();
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param \Psr\Cache\CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     * False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     * @throws \Exception
     */
    public function saveDeferred(\Psr\Cache\CacheItemInterface $item)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     * True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     * @throws \Exception
     */
    public function commit()
    {
        throw new \Exception('Not implemented');
    }
}
