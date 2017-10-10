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

namespace PrestaShop\PrestaShop\Adapter\Cache;

use Memcache;
use Memcached;
use Doctrine\DBAL\Connection;

/**
 * This class manages Memcache(d) servers in "Configure > Advanced Parameters > Performance" page.
 */
class MemcacheServerManager
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->tableName = $dbPrefix.'memcached_servers';
    }

    /**
     * Add a memcache server
     *
     * @param string $serverIp
     * @param int $serverPort
     * @param int $serverWeight
     * @return void
     */
    public function addServer($serverIp, $serverPort, $serverWeight)
    {
        $this->connection->executeUpdate('INSERT INTO '. $this->tableName .' (ip, port, weight) VALUES(:serverIp, :serverPort, :serverWeight)', array(
           'serverIp' => $serverIp,
           'serverPort' => (int) $serverPort,
           'serverWeight' => (int) $serverWeight
        ));

        return array(
            'id' => $this->connection->lastInsertId(),
            'server_ip' => $serverIp,
            'server_port' => $serverPort,
            'server_weight' => $serverWeight,
        );
    }

    /**
     * Test if a Memcache configuration is valid
     *
     * @param string $serverIp
     * @param string @serverPort
     *
     * @return bool
     */
    public function testConfiguration($serverIp, $serverHost)
    {
        if (extension_loaded('memcached')) {
            $memcached = new Memcached();
            $memcached->addServer($serverIp, $serverHost);

            return false === in_array('255.255.255', $memcached->getVersion(), true);
        }

        return true === @memcache_connect($serverIp, $serverHost);
    }

    /**
     * Delete a memcache server (a deletion returns the number of rows deleted)
     *
     * @param int $serverId_server id (in database)
     * @return bool
     */
    public function deleteServer($serverId)
    {
        $deletionSuccess = $this->connection->delete($this->tableName, array('id_memcached_server' => $serverId));

        return 1 === $deletionSuccess;
    }

    /**
     * Get list of memcached servers
     *
     * @return array
     */
    public function getServers()
    {
        return $this->connection->fetchAll('SELECT * FROM '. $this->tableName, array());
    }
}
