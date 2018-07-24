<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace Tests\TestCase;

use PrestaShop\PrestaShop\Core\Configuration\AdvancedConfigurationInterface;
use Exception;

class FakeConfiguration implements AdvancedConfigurationInterface
{
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function get($key, $default = null)
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new Exception("Key $key does not exist in the fake configuration.");
        }
        return $this->keys[$key];
    }

    public function set($key, $value)
    {
        $this->keys[$key] = $value;
        return $this;
    }

    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default);
    }

    public function getBool($key, $default = false)
    {
        return (bool) $this->get($key, $default);
    }

    public function all()
    {
        return $this->keys;
    }

    public function keys()
    {
       return array_keys($this->keys);
    }

    public function has($key)
    {
        return isset($this->keys[$key]);
    }

    public function count()
    {
        return count($this->keys);
    }

    public function remove($key)
    {
        if (isset($this->keys[$key])) {
            unset($this->keys[$key]);
        }

        return true;
    }
}
