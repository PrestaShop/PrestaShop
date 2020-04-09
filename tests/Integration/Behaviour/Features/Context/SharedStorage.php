<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use RuntimeException;

/**
 * Shared storage to use between behat contexts
 */
class SharedStorage
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * @var array
     */
    private $storage = [];

    /**
     * Used for accessing latest resource.
     *
     * @var string|null
     */
    private $latestKey;

    /**
     * @return self
     */
    public static function getStorage()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->exists($key)) {
            throw new RuntimeException(sprintf('Item with key "%s" does not exist', $key));
        }

        return $this->storage[$key];
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getWithDefault($key, $default)
    {
        if (!isset($this->storage[$key])) {
            return $default;
        }

        return $this->storage[$key];
    }

    /**
     * @param string $key
     * @param mixed $resource
     */
    public function set($key, $resource)
    {
        $this->storage[$key] = $resource;
        $this->latestKey = $key;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function exists($key): bool
    {
        return isset($this->storage[$key]);
    }

    /**
     * @param string $key
     */
    public function clear($key)
    {
        if ($this->exists($key)) {
            unset($this->storage[$key]);
        }
    }

    /**
     * Get the resource that was the latest one to be set into the storage.
     *
     * @return mixed
     */
    public function getLatestResource()
    {
        if (!array_key_exists($this->latestKey, $this->storage)) {
            throw new RuntimeException(sprintf('Latest resource with key "%s" does not exist.', $this->latestKey));
        }

        return $this->storage[$this->latestKey];
    }
}
