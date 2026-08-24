<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Db;
use DbDoctrine;
use DbPDOCore;
use Doctrine\DBAL\Connection;
use PrestaShopException;

/**
 * Keeps the legacy Db singleton and Doctrine's DBAL connection sharing the same physical connection, so
 * that both layers participate in a single database transaction instead of two independent ones.
 */
class ConnectionSwitcher
{
    /**
     * The connection is optional: this class is also built by PrestaShop\PrestaShop\Core\Foundation\IoC\Container
     * (see Core\ContainerBuilder), a reflection-based container with no notion of Symfony services, which cannot
     * supply one. Without it, switchConnection() just skips sharing the connection with Doctrine.
     */
    public function __construct(
        private readonly ?Connection $connection = null,
    ) {
    }

    /**
     * Returns the legacy Db instance. When a Doctrine connection was injected and the legacy Db is
     * PDO-based, this instance is guaranteed to be a DbDoctrine sharing the same underlying PDO
     * connection as Doctrine, so that both layers participate in a single physical database
     * transaction. Without a Doctrine connection, or when the legacy Db isn't PDO-based (e.g. the
     * mysqli fallback, see Db::getClass()), this just returns the legacy Db instance untouched, since
     * there is then no PDO connection to share.
     *
     * @throws PrestaShopException if replacing the current legacy Db instance would silently discard an
     *                             uncommitted transaction, or if the configured Doctrine DBAL driver isn't
     *                             PDO-based (see DbDoctrine::connect())
     */
    public function switchConnection(): Db
    {
        $db = Db::getInstance();

        if (null === $this->connection || !$db instanceof DbPDOCore) {
            return $db;
        }

        // Checked unconditionally, before deciding whether to reuse or rebuild below: a pending
        // transaction is just as unsafe to replace when $db is already a DbDoctrine (e.g. one left
        // over from a previous, no-longer-current Doctrine connection) as when it's a plain DbPDO.
        if ($db->hasUncommittedTransaction()) {
            throw new PrestaShopException('Cannot share the Doctrine connection: the legacy Db connection it would replace has an uncommitted transaction.');
        }

        if ($db instanceof DbDoctrine && $db->isSharing($this->connection)) {
            $db->connect();

            return $db;
        }

        /** @var DbPDOCore $doctrineDb */
        $doctrineDb = new DbDoctrine($this->connection);
        $doctrineDb->connect();
        Db::$instance[0] = $doctrineDb;

        return $doctrineDb;
    }
}
