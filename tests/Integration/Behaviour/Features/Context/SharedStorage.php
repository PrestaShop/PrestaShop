<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
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
        if (!isset($this->storage[$key])) {
            throw new RuntimeException(sprintf('Item with key "%s" does not exist', $key));
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
    }

    /**
     * @param string $key
     */
    public function clear($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
        }
    }
}
