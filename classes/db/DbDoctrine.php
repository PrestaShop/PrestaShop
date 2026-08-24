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
     * Unlike DbPDOCore::connect(), this always re-reads the Doctrine connection instead of skipping
     * when $this->link is already set. That matters after a lost-connection reconnect (see
     * DbCore::query()'s error-2006 handling, which calls disconnect() then connect() on this same
     * object): re-pulling from Doctrine here means the two layers try to share a connection again
     * instead of silently degrading to a legacy-only one. This only actually recovers a dead
     * connection because disconnect() (below) closes the Doctrine connection too — Doctrine has no
     * way to notice, on its own, that the physical connection it shares with the legacy side died.
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

    /**
     * Also closes the Doctrine connection, not just this legacy instance's link to it.
     *
     * Doctrine's Connection::connect() is a no-op once it holds a native connection, even a dead one:
     * it only reconnects after close() resets its internal state. Without closing it here too,
     * DbCore::query()'s error-2006 recovery (disconnect() then connect()) would call connect() above,
     * which would just get handed back the same dead native connection Doctrine still thinks is fine.
     *
     * @see DbCore::disconnect()
     */
    public function disconnect()
    {
        $this->connection->close();
        parent::disconnect();
    }
}
