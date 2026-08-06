<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Doctrine\Middleware;

use DateTime;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use SensitiveParameter;

/**
 * Aligns the MySQL session time zone with PHP's current time zone offset on every
 * Doctrine DBAL connection, so that SQL directives such as NOW() or CURRENT_TIMESTAMP
 * evaluate in the shop time zone configured in PHP rather than the MySQL server time
 * zone (UTC by default). See issue #30828.
 *
 * A numeric offset (e.g. "+02:00") is used on purpose: it requires no MySQL time zone
 * tables and is recomputed on each connection, so DST is always correct at connect time.
 *
 * This mirrors the legacy Db::setTimeZone() behaviour for the Symfony/CQRS connection.
 */
final class SetSessionTimeZoneMiddleware implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        return new class($driver) extends AbstractDriverMiddleware {
            public function connect(
                #[SensitiveParameter]
                array $params
            ): Connection {
                $connection = parent::connect($params);

                $offset = (new DateTime())->format('P');
                // Defensive: only ever inject a well-formed offset into the statement.
                if (preg_match('/^[+-]\d{2}:\d{2}$/', $offset)) {
                    $connection->exec("SET SESSION time_zone = '" . $offset . "'");
                }

                return $connection;
            }
        };
    }
}
