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

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Adapter\Entity\CacheMemcached;
use Validate;
use Cache;

class CacheCoreTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $memcached = new CacheMemcached();
        Cache::setInstanceForTesting($memcached);
        Cache::getInstance()->setMaxCachedObjectsByTable(5);
    }

    public function tearDown() {
        Cache::getInstance()->setMaxCachedObjectsByTable(10000);
    }

    public function testSetQuery()
    {
        $queries = $this->selectDataProvider();
        foreach($queries as $query) {
            Cache::getInstance()->set(Cache::getInstance()->getQueryHash($query), 1);
        }
    }

    // --- providers ---

    public function selectDataProvider()
    {
        $selectArray = array();

        for($i = 0; $i <= 100000; $i++) {
            $selectArray[] = 'SELECT name FROM ps_configuration WHERE id = '.$i;
        }

        return $selectArray;
    }
}
