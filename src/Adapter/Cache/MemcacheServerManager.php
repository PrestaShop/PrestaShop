<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        $this->connection->executeStatement('INSERT INTO ' . $this->tableName . ' (ip, port, weight) VALUES(:serverIp, :serverPort, :serverWeight)', [
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
     * @param int $serverPort
     *
     * @return bool
     */
    public function testConfiguration($serverIp, $serverPort)
    {
        if (extension_loaded('memcached')) {
            $memcached = new Memcached();
            $memcached->addServer($serverIp, (int) $serverPort);
            $version = $memcached->getVersion();

            return is_array($version) && false === in_array('255.255.255', $version, true);
        }

        $memcache = new Memcache();

        return true === $memcache->connect($serverIp, (int) $serverPort);
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
        return $this->connection->fetchAllAssociative('SELECT * FROM ' . $this->tableName);
    }
}
