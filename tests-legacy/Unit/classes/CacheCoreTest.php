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

namespace LegacyTests\Unit\Classes;

use PrestaShop\PrestaShop\Adapter\Entity\CacheMemcache;
use PHPUnit_Framework_TestCase;
use Cache;

class CacheCoreTest extends PHPUnit_Framework_TestCase
{
    private $cacheArray = array();

    public function setUp()
    {
        parent::setUp();

        $memcachedMock = $this->getMockBuilder('CacheMemcache')
            ->setMethods(array('_set', '_get', 'isConnected', '_delete', '_deleteMulti'))
            ->getMock();

        $memcachedMock->method('isConnected')->willReturn(true);
        $memcachedMock->method('_get')->willReturnCallback(array($this, 'getFromArray'));
        $memcachedMock->method('_set')->willReturnCallback(array($this, 'setIntoArray'));
        $memcachedMock->method('_delete')->willReturnCallback(array($this, 'deleteFromArray'));
        $memcachedMock->method('_deleteMulti')->willReturnCallback(array($this, 'deleteMultiFromArray'));

        Cache::setInstanceForTesting($memcachedMock);
    }

    public function tearDown()
    {
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

        foreach ($args[0] as $arg) {
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

    /**
     * When we set a query into cache
     * Then the cache should contain an entry with the cache key and its result
     * AND entries which link each table from the query to the cache key
     */
    public function testSetQuery()
    {
        $queries = $this->selectDataProvider();
        foreach ($queries as $query) {
            Cache::getInstance()->setQuery($query, array('queryResult'));
        }

        foreach ($queries as $query) {
            $queryHash = Cache::getInstance()->getQueryHash($query);

            // check the query is in the cache
            $this->assertArrayHasKey($queryHash, $this->cacheArray);

            $tableLists = Cache::getInstance()->getTables($query);
            foreach ($tableLists as $table) {
                $tableCacheKey = Cache::getInstance()->getTableMapCacheKey($table);

                // check the table cache key is in the cache
                $this->assertArrayHasKey($tableCacheKey, $this->cacheArray);

                // check the query hash is in the table map
                $this->assertArrayHasKey($queryHash, $this->cacheArray[$tableCacheKey]);
            }
        }
    }

    /**
     * When we set a query into cache AND the cache is full
     * Then we should make room into the cache by removing some entries
     * No LRU strategy used here
     */
    public function testSetQueryWithCacheFull()
    {
        Cache::getInstance()->setMaxCachedObjectsByTable(2);
        
        $queries = $this->selectDataProvider();
        $i = 0;
        foreach ($queries as $query) {
            $i++;
            Cache::getInstance()->setQuery($query, array('queryResult '.$i));
        }

        $this->assertCount(4, $this->cacheArray);

        foreach ($queries as $query) {
            $tableLists = Cache::getInstance()->getTables($query);
            foreach ($tableLists as $table) {
                $tableCacheKey = Cache::getInstance()->getTableMapCacheKey($table);

                // check the table cache key is in the cache
                $this->assertArrayHasKey($tableCacheKey, $this->cacheArray);

                // check the query hash is in the table map
                $this->assertCount(2, $this->cacheArray[$tableCacheKey]);
            }
            break;
        }

        // check the cache only contains two keys (+ 2 table keys)
        $this->assertCount(4, $this->cacheArray);
    }

    /**
     * When we set a query into cache AND the cache is full
     * Then we should make room into the cache by removing some entries using a LRU strategy.
     * We verify the LRU strategy is working properly
     */
    public function testCacheLRUWithCacheFull()
    {
        Cache::getInstance()->setMaxCachedObjectsByTable(4);

        $queries = $this->selectDataProvider();
        $i = 0;
        foreach ($queries as $query) {
            Cache::getInstance()->setQuery($query, array('queryResult '.$i));
            if ($i == 3) {
                break;
            }
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

    /**
     * When the cache is invalidated for a given table
     * Then all the associated queries should be invalidated,
     * AND if those queries are present into other table <=> query map (in case of a join for example)
     * Then the entries from those maps should be removed as well
     */
    public function testCacheInvalidation()
    {
        $queries = $this->selectDataProvider();
        foreach ($queries as $query) {
            Cache::getInstance()->setQuery($query, array('queryResult'));
        }

        Cache::getInstance()->setQuery('SELECT name FROM ps_confiture WHERE id = 1', array('queryResultExtra'));

        $tableMapKey = Cache::getInstance()->getTableMapCacheKey('ps_configuration');
        $invalidatedKeys = $this->cacheArray[$tableMapKey];

        $this->assertArrayHasKey($tableMapKey, $this->cacheArray);
        
        Cache::getInstance()->deleteQuery('SELECT name FROM ps_configuration WHERE id = 1');

        $this->assertArrayNotHasKey($tableMapKey, $this->cacheArray);

        foreach (array_keys($invalidatedKeys) as $invalidatedKey) {
            $this->assertArrayNotHasKey($invalidatedKey, $this->cacheArray);
        }

        $validTableMapKey = Cache::getInstance()->getTableMapCacheKey('ps_confiture');
        $this->assertArrayHasKey($validTableMapKey, $this->cacheArray);

        Cache::getInstance()->deleteQuery('SELECT name FROM ps_confiture WHERE id = 1');

        $this->assertArrayNotHasKey($validTableMapKey, $this->cacheArray);

        // now check invalidation why full deletion of entry from "other table"
        foreach ($queries as $query) {
            Cache::getInstance()->setQuery($query, array('queryResult'));
        }

        $tableMapKey = Cache::getInstance()->getTableMapCacheKey('ps_configuration');
        $invalidatedKeys = $this->cacheArray[$tableMapKey];

        $this->assertArrayHasKey($tableMapKey, $this->cacheArray);

        // all entries from both ps_configuration AND ps_confiture will be deleted
        Cache::getInstance()->deleteQuery('SELECT name FROM ps_configuration WHERE id = 1');

        $this->assertArrayNotHasKey($tableMapKey, $this->cacheArray);
        $otherTableMapKey = Cache::getInstance()->getTableMapCacheKey('ps_confiture');
        $this->assertArrayNotHasKey($otherTableMapKey, $this->cacheArray);
    }

    private function checkTableCacheMapCounter($query, $counter)
    {
        $queryHash = Cache::getInstance()->getQueryHash($query);
        $tableLists = Cache::getInstance()->getTables($query);
        foreach ($tableLists as $table) {
            $tableCacheKey = Cache::getInstance()->getTableMapCacheKey($table);

            // check the table cache key is in the cache
            $this->assertArrayHasKey($tableCacheKey, $this->cacheArray);

            // check the query hash is in the table map
            $this->assertEquals($counter, $this->cacheArray[$tableCacheKey][$queryHash]['count']);
        }
    }


    // --- providers ---

    public function selectDataProvider()
    {
        $selectArray = array();

        for ($i = 0; $i <= 9; $i++) {
            $selectArray[] = 'SELECT name FROM ps_configuration LEFT JOIN ps_confiture WHERE id = '.$i;
        }

        return $selectArray;
    }
}
