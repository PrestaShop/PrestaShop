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

namespace PrestaShop\PrestaShop\Adapter\Cache;

use Doctrine\DBAL\Connection;
use Memcache;
use Memcached;

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
        $this->tableName = $dbPrefix . 'memcached_servers';
    }

    /**
     * Add a memcache server.
     *
     * @param string $serverIp
     * @param int $serverPort
     * @param int $serverWeight
     */
    public function addServer($serverIp, $serverPort, $serverWeight)
    {
        $this->connection->executeUpdate('INSERT INTO ' . $this->tableName . ' (ip, port, weight) VALUES(:serverIp, :serverPort, :serverWeight)', [
            'serverIp' => $serverIp,
            'serverPort' => (int) $serverPort,
            'serverWeight' => (int) $serverWeight,
        ]);

        return [
            'id' => $this->connection->lastInsertId(),
            'server_ip' => $serverIp,
            'server_port' => $serverPort,
            'server_weight' => $serverWeight,
        ];
    }

    /**
     * Test if a Memcache configuration is valid.
     *
     * @param string $serverIp
     * @param string $serverPort
     *
     * @return bool
     */
    public function testConfiguration($serverIp, $serverPort)
    {
        if (extension_loaded('memcached')) {
            $memcached = new Memcached();
            $memcached->addServer($serverIp, $serverPort);
            $version = $memcached->getVersion();

            return is_array($version) && false === in_array('255.255.255', $version, true);
        }

        $memcache = new Memcache();

        return true === $memcache->connect($serverIp, $serverPort);
    }

    /**
     * Delete a memcache server (a deletion returns the number of rows deleted).
     *
     * @param int $serverId Server ID (in database)
     *
     * @return bool
     */
    public function deleteServer($serverId)
    {
        $deletionSuccess = $this->connection->delete($this->tableName, ['id_memcached_server' => $serverId]);

        return 1 === $deletionSuccess;
    }

    /**
     * Get list of memcached servers.
     *
     * @return array
     */
    public function getServers()
    {
        return $this->connection->fetchAll('SELECT * FROM ' . $this->tableName, []);
    }
}
