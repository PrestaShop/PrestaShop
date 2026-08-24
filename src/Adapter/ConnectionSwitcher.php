<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Db;
use DbDoctrine;
use DbDoctrineCore;
use DbPDOCore;
use Doctrine\DBAL\Connection;
use PrestaShopException;

/**
 * Keeps the legacy Db singleton and Doctrine's DBAL connection sharing the same physical connection, so
 * that both layers participate in a single database transaction instead of two independent ones.
 */
class ConnectionSwitcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * Returns the legacy Db instance. When the legacy Db is PDO-based, this instance is guaranteed to
     * be a DbDoctrine sharing the same underlying PDO connection as Doctrine, so that both layers
     * participate in a single physical database transaction. When the legacy Db isn't PDO-based (e.g.
     * the mysqli fallback, see Db::getClass()), this just returns the legacy Db instance untouched,
     * since there is then no PDO connection to share.
     *
     * Safe to call re-entrantly (e.g. a transactional handler dispatching another transactional
     * command): reusing an already-shared DbDoctrine is checked for first and is a no-op, so a nested
     * call never trips the uncommitted-transaction guard below over the transaction it is itself
     * already part of. That guard only ever fires on the path that would actually replace the legacy
     * Db instance with a new one.
     *
     * @throws PrestaShopException if replacing the current legacy Db instance would silently discard an
     *                             uncommitted transaction, or if the configured Doctrine DBAL driver isn't
     *                             PDO-based (see DbDoctrine::connect())
     */
    public function switchConnection(): Db
    {
        $db = Db::getInstance();

        if (!$db instanceof DbPDOCore) {
            return $db;
        }

        // Tested against DbDoctrineCore, not DbDoctrine: the legacy class loader generates
        // `class DbDoctrine extends DbDoctrineCore {}`, so the Core class is the base and the
        // leaf is the subclass. Checking the leaf would miss any other DbDoctrineCore subclass
        // (an override, a test double) and silently drop it into the rebuild path below.
        if ($db instanceof DbDoctrineCore && $db->isSharing($this->connection)) {
            $db->connect();

            return $db;
        }

        if ($db->hasUncommittedTransaction()) {
            throw new PrestaShopException('Cannot share the Doctrine connection: the legacy Db connection it would replace has an uncommitted transaction.');
        }

        /** @var DbPDOCore $doctrineDb */
        $doctrineDb = new DbDoctrine($this->connection);
        $doctrineDb->connect();
        Db::$instance[0] = $doctrineDb;

        return $doctrineDb;
    }
}
