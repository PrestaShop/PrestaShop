<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

/**
 * Class DbDoctrineCore.
 *
 * Legacy Db implementation backed by a Doctrine DBAL connection, so that legacy code and Doctrine
 * participate in the very same physical connection/transaction instead of two independent ones.
 */
class DbDoctrineCore extends DbPDOCore
{
    public function __construct(
        private readonly Connection $connection,
    ) {
        parent::__construct('', '', '', '', false);
    }

    /**
     * Shares the native PDO connection behind the injected Doctrine connection.
     *
     * Deliberately does not override disconnect() to also close the Doctrine connection: DbCore's
     * error-2006 recovery (disconnect() then connect() on this same object) can't actually reach a
     * live driver exception on PHP 8+ (PDO defaults to ERRMODE_EXCEPTION and _query() converts that
     * to a thrown PrestaShopException before Db::query() ever inspects $this->result), so there is no
     * live reconnect scenario to fix here — and closing Doctrine's connection from disconnect() would
     * also fire from DbCore::__destruct() on ordinary teardown, discarding Doctrine's own transaction
     * bookkeeping for a connection this instance doesn't own.
     *
     * @see DbCore::connect()
     *
     * @return PDO
     *
     * @throws PrestaShopException if the Doctrine connection isn't backed by a native PDO instance,
     *                             since there would then be no native PDO connection to share with
     *                             the legacy Db instance
     */
    public function connect()
    {
        $nativeConnection = $this->connection->getNativeConnection();
        if (!$nativeConnection instanceof PDO) {
            throw new PrestaShopException(sprintf(
                'Expected the Doctrine DBAL connection to be backed by a PDO instance to share it with the legacy Db, got %s instead.',
                get_debug_type($nativeConnection)
            ));
        }

        $this->setPDO($nativeConnection);

        return $this->link;
    }

    /**
     * Whether this instance is sharing the given Doctrine connection, as opposed to one from an
     * earlier, no-longer-current Doctrine connection (e.g. a Symfony kernel reboot builds a brand new
     * Connection service, but this legacy Db singleton survives across that reboot).
     */
    public function isSharing(Connection $connection): bool
    {
        return $this->connection === $connection;
    }
}
