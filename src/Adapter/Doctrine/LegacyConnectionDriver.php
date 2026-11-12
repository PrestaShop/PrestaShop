<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Doctrine;

use Db;
use Doctrine\DBAL\Driver\AbstractMySQLDriver;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use PDO;
use SensitiveParameter;

/**
 * Reuses the legacy Db PDO link when both target the same database, so a request opens a single
 * MySQL connection and Doctrine and legacy code share one session (sql_mode, time zone, transactions).
 */
final class LegacyConnectionDriver extends AbstractMySQLDriver
{
    public function connect(
        #[SensitiveParameter]
        array $params
    ): DriverConnection {
        // Only an already opened master link is reused: connection failures keep surfacing as DBAL exceptions
        if (isset(Db::$instance[0]) && $this->targetsLegacyDatabase($params)) {
            $link = Db::$instance[0]->getLink();
            if ($link instanceof PDO) {
                return new SharedLegacyConnection($link);
            }
        }

        return (new Driver())->connect($params);
    }

    private function targetsLegacyDatabase(
        #[SensitiveParameter]
        array $params
    ): bool {
        if (!defined('_DB_SERVER_')) {
            return false;
        }

        $server = $params['host'] ?? '';
        if (!empty($params['port'])) {
            $server .= ':' . $params['port'];
        }

        return $server === _DB_SERVER_
            && ($params['dbname'] ?? null) === _DB_NAME_
            && ($params['user'] ?? null) === _DB_USER_;
    }
}
