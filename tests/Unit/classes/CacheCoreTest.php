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

use PrestaShop\PrestaShop\Adapter\Entity\CacheMemcache;
use PrestaShop\PrestaShop\Tests\Unit\UnitTestCase;
use Cache;

class CacheCoreTest extends UnitTestCase
{
    private $cacheArray = array();

    public function setUp() {
        parent::setUp();

        $memcachedMock = $this->getMockBuilder(CacheMemcache::class)
            ->setMethods(['_set', '_get', 'isConnected', '_delete', '_deleteMulti'])
            ->getMock();

        $memcachedMock->method('isConnected')->willReturn(true);
        $memcachedMock->method('_get')->willReturnCallback(array($this, 'getFromArray'));
        $memcachedMock->method('_set')->willReturnCallback(array($this, 'setIntoArray'));
        $memcachedMock->method('_delete')->willReturnCallback(array($this, 'deleteFromArray'));
        $memcachedMock->method('_deleteMulti')->willReturnCallback(array($this, 'deleteMultiFromArray'));

        Cache::setInstanceForTesting($memcachedMock);
    }

    public function tearDown() {
        $this->cacheArray = array();
    }

    public function getFromArray()
    {
        $args = func_get_args();

        if (isset($this->cacheArray[$args[0]])) {
            return $this->cacheArray[$args[0]];
        } else {
            return null;
        }
    }

    public function deleteMultiFromArray()
    {
        $args = func_get_args();

        foreach($args[0] as $arg) {
            unset($this->cacheArray[$arg]);
        }
    }

    public function deleteFromArray()
    {
        $args = func_get_args();

        unset($this->cacheArray[$args[0]]);
    }

    public function setIntoArray()
    {
        $args = func_get_args();

        $this->cacheArray[$args[0]] = $args[1];
    }

    public function testSetQuery()
    {
        $queries = $this->selectDataProvider();
        foreach($queries as $query) {
            Cache::getInstance()->setQuery($query, array('queryResult'));
        }

        foreach($queries as $query) {
            $queryHash = Cache::getInstance()->getQueryHash($query);

            // check the query is in the cache
            $this->assertArrayHasKey($queryHash, $this->cacheArray);

            $tableLists = Cache::getInstance()->getTables($query);
            foreach($tableLists as $table) {
                $tableCacheKey = Cache::getInstance()->getTableMapCacheKey($table);

                // check the table cache key is in the cache
                $this->assertArrayHasKey($tableCacheKey, $this->cacheArray);

                // check the query hash is in the table map
                $this->assertArrayHasKey($queryHash, $this->cacheArray[$tableCacheKey]);
            }
        }
    }

    public function testSetQueryWithCacheFull()
    {
        Cache::getInstance()->setMaxCachedObjectsByTable(2);
        
        $queries = $this->selectDataProvider();
        $i = 0;
        foreach($queries as $query) {
            $i++;
            Cache::getInstance()->setQuery($query, array('queryResult '.$i));
        }

        $this->assertCount(4, $this->cacheArray);

        foreach($queries as $query) {
            $tableLists = Cache::getInstance()->getTables($query);
            foreach($tableLists as $table) {
                $tableCacheKey = Cache::getInstance()->getTableMapCacheKey($table);

                // check the table cache key is in the cache
                $this->assertArrayHasKey($tableCacheKey, $this->cacheArray);

                // check the query hash is in the table map
                $this->assertCount(2, $this->cacheArray[$tableCacheKey]);
            }
            break;
        }

        // check the cache only contains the two latest key
        $queryHash = Cache::getInstance()->getQueryHash($queries[8]);
        $this->assertArrayHasKey($queryHash, $this->cacheArray);

        $queryHash = Cache::getInstance()->getQueryHash($queries[9]);
        $this->assertArrayHasKey($queryHash, $this->cacheArray);
    }

    public function testCacheLRUWithCacheFull()
    {
        Cache::getInstance()->setMaxCachedObjectsByTable(4);

        $queries = $this->selectDataProvider();
        $i = 0;
        foreach($queries as $query) {
            Cache::getInstance()->setQuery($query, array('queryResult '.$i));
            if ($i == 3) break;
            $i++;
        }

        // increment the counter for query 1 and 3
        Cache::getInstance()->incrementQueryCounter($queries[1]);
        Cache::getInstance()->incrementQueryCounter($queries[3]);
        Cache::getInstance()->incrementQueryCounter($queries[3]);

        $this->assertCount(6, $this->cacheArray);

        // inserting a new entry should update the query counter, and the LRU logic
        // should evict 0 and 2 query from the cache
        Cache::getInstance()->setQuery($queries[4], array('queryResult 4'));

        // check the cache only contains the query 1 3 and 4
        $queryHash = Cache::getInstance()->getQueryHash($queries[1]);
        $this->assertArrayHasKey($queryHash, $this->cacheArray);

        $queryHash = Cache::getInstance()->getQueryHash($queries[3]);
        $this->assertArrayHasKey($queryHash, $this->cacheArray);

        $queryHash = Cache::getInstance()->getQueryHash($queries[4]);
        $this->assertArrayHasKey($queryHash, $this->cacheArray);

        $queryHash = Cache::getInstance()->getQueryHash($queries[0]);
        $this->assertArrayNotHasKey($queryHash, $this->cacheArray);

        $queryHash = Cache::getInstance()->getQueryHash($queries[2]);
        $this->assertArrayNotHasKey($queryHash, $this->cacheArray);

        // 1 should have a counter set to 2
        $this->checkTableCacheMapCounter($queries[1], 2);

        // 3 should have a counter set to 3
        $this->checkTableCacheMapCounter($queries[3], 3);

        // 4 should have a counter set to 1
        $this->checkTableCacheMapCounter($queries[4], 1);
    }

    private function checkTableCacheMapCounter($query, $counter)
    {
        $queryHash = Cache::getInstance()->getQueryHash($query);
        $tableLists = Cache::getInstance()->getTables($query);
        foreach($tableLists as $table) {
            $tableCacheKey = Cache::getInstance()->getTableMapCacheKey($table);

            // check the table cache key is in the cache
            $this->assertArrayHasKey($tableCacheKey, $this->cacheArray);

            // check the query hash is in the table map
            $this->assertEquals($counter, $this->cacheArray[$tableCacheKey][$queryHash]);
        }
    }


    // --- providers ---

    public function selectDataProvider()
    {
        $selectArray = array();

        for($i = 0; $i <= 9; $i++) {
            $selectArray[] = 'SELECT name FROM ps_configuration LEFT JOIN ps_confiture WHERE id = '.$i;
        }

        return $selectArray;
    }
}
