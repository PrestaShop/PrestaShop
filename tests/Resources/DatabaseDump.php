<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Resources;

use Cache;
use CMSCategory;
use CMSRole;
use Combination;
use Connection;
use Db;
use Exception;
use Language;
use Order;
use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShop\PrestaShop\Core\Version;
use PrestaShopLogger;
use ProductAttribute;

class DatabaseDump
{
    /**
     * Database host
     *
     * @var string
     */
    private $host;

    /**
     * Database port
     *
     * @var int|string
     */
    private $port;

    /**
     * Database user
     *
     * @var string
     */
    private $user;

    /**
     * Database password
     *
     * @var string
     */
    private $password;

    /**
     * Database name
     *
     * @var string
     */
    private $databaseName;

    /**
     * Database prefix for table names
     *
     * @var string
     */
    private $dbPrefix;

    /**
     * Generic dump file path (dump of the whole database)
     *
     * @var string
     */
    private $dumpFile;

    /**
     * Db instance to perform queries
     *
     * @var Db
     */
    private $db;

    /**
     * Constructor extracts database connection info from PrestaShop's configuration,
     * but we use mysqldump and mysql for dump / restore.
     *
     * @param string $dumpFile dump file name
     */
    private function __construct($dumpFile = null)
    {
        $host_and_maybe_port = explode(':', _DB_SERVER_);

        if (count($host_and_maybe_port) === 1) {
            $this->host = $host_and_maybe_port[0];
            /* @phpstan-ignore-next-line */
            $this->port = _DB_TYPE_ == 'pgsql' ? 5432 : 3306;
        } elseif (count($host_and_maybe_port) === 2) {
            $this->host = $host_and_maybe_port[0];
            $this->port = $host_and_maybe_port[1];
        }

        $this->databaseName = _DB_NAME_;
        if ($dumpFile === null) {
            $this->dumpFile = sprintf('%s/ps_dump_%s_%s.sql', sys_get_temp_dir(), $this->databaseName, Version::VERSION);
        } else {
            $this->dumpFile = $dumpFile;
        }
        $this->user = _DB_USER_;
        $this->password = _DB_PASSWD_;
        $this->dbPrefix = _DB_PREFIX_;
        $this->db = Db::getInstance();
    }

    /**
     * Restore the dump to the actual database.
     */
    public function restore(): void
    {
        $this->checkDumpFile();

        $restoreCommand = $this->buildRestoreCommand();
        $restoreCommand .= ' < ' . escapeshellarg($this->dumpFile) . ' 2>&1';
        $this->exec($restoreCommand);

        // Clean EntityManager cache
        Cache::clean('objectmodel_*');
    }

    /**
     * Restore a specific table in the database.
     *
     * @param string $table
     */
    public function restoreTable(string $table): void
    {
        $className = $this->getClassName($table);
        $this->cleanClassCache($className);
        $tableName = $this->dbPrefix . $table;
        $this->checkTableDumpFile($tableName);

        $dumpChecksum = file_get_contents($this->getTableChecksumPath($tableName));
        $checksum = $this->getTableChecksum($tableName);
        // Table was not modified, no need to restore
        if ($checksum === $dumpChecksum) {
            return;
        }

        $dumpFile = $this->getTableDumpPath($tableName);
        $restoreCommand = $this->buildRestoreCommand();
        $restoreCommand .= ' < ' . escapeshellarg($dumpFile) . ' 2>&1';
        $this->exec($restoreCommand);
    }

    private function cleanClassCache(string $className): void
    {
        // Clean EntityManager cache
        Cache::clean(sprintf('objectmodel_%s_*', $className));
        // Clear static cache of the ObjectModel class related to the table
        $staticMethodCall = sprintf('%s::resetStaticCache', $className);
        if (is_callable($staticMethodCall)) {
            call_user_func($staticMethodCall);
        }
    }

    private function getClassName(string $table): string
    {
        if ($table === 'lang') {
            return Language::class;
        } elseif ($table === 'cms_category') {
            return CMSCategory::class;
        } elseif ($table === 'cms_role') {
            return CMSRole::class;
        } elseif ($table === 'product_attribute') {
            return Combination::class;
        } elseif ($table === 'connections') {
            return Connection::class;
        } elseif ($table === 'log') {
            return PrestaShopLogger::class;
        } elseif ($table === 'attribute') {
            return ProductAttribute::class;
        } elseif ($table === 'orders') {
            return Order::class;
        }

        return Inflector::getInflector()->classify($table);
    }

    /**
     * Builds the command used to restore a dump into the database (reads the dump from stdin,
     * appended by the caller as `... < dumpfile`).
     */
    private function buildRestoreCommand(): string
    {
        /* @phpstan-ignore-next-line */
        if (_DB_TYPE_ == 'pgsql') {
            $parts = array_merge(
                $this->getPgsqlDefaultParameters('psql'),
                ['-v', 'ON_ERROR_STOP=1', escapeshellarg($this->databaseName)]
            );

            return implode(' ', $parts);
        }

        $parts = array_merge($this->getMysqlDefaultParameters('mysql'), [escapeshellarg($this->databaseName)]);

        return implode(' ', $parts);
    }

    /**
     * Builds the command used to dump the database (or a single table) to $dumpfile.
     */
    private function buildDumpCommand(string $dumpfile, ?string $table = null): string
    {
        /* @phpstan-ignore-next-line */
        if (_DB_TYPE_ == 'pgsql') {
            $parts = array_merge(
                $this->getPgsqlDefaultParameters('pg_dump'),
                ['-f', escapeshellarg($dumpfile), '--clean', '--if-exists', '--column-inserts']
            );
            if (null !== $table) {
                $parts[] = '-t';
                $parts[] = escapeshellarg($table);
            }
            $parts[] = escapeshellarg($this->databaseName);

            return implode(' ', $parts);
        }

        $parts = array_merge($this->getMysqlDefaultParameters('mysqldump'), ['-r', escapeshellarg($dumpfile), escapeshellarg($this->databaseName)]);
        if (null !== $table) {
            $parts[] = escapeshellarg($table);
        }
        $parts[] = '--complete-insert';

        return implode(' ', $parts);
    }

    /**
     * @return string[]
     */
    private function getMysqlDefaultParameters(string $executable): array
    {
        $parts = [
            $executable,
            '-u', escapeshellarg($this->user),
            '-P', escapeshellarg($this->port),
            '-h', escapeshellarg($this->host),
        ];

        if ($this->password) {
            $parts[] = '-p' . escapeshellarg($this->password);
        }

        return $parts;
    }

    /**
     * psql/pg_dump use -U for the user (-u doesn't exist) and -p for the port (not the
     * password); the password can only be passed via the PGPASSWORD environment variable.
     *
     * @return string[]
     */
    private function getPgsqlDefaultParameters(string $executable): array
    {
        $parts = [];
        if ($this->password) {
            $parts[] = 'PGPASSWORD=' . escapeshellarg($this->password);
        }

        $parts[] = $executable;
        $parts[] = '-U';
        $parts[] = escapeshellarg($this->user);
        $parts[] = '-p';
        $parts[] = escapeshellarg($this->port);
        $parts[] = '-h';
        $parts[] = escapeshellarg($this->host);

        return $parts;
    }

    /**
     * Like exec, but will raise an exception if the command failed.
     *
     * @param string $command
     *
     * @return array
     *
     * @throws Exception
     */
    private function exec($command): array
    {
        $output = [];
        $ret = 1;
        exec($command, $output, $ret);

        if ($ret !== 0) {
            throw new Exception(sprintf('Unable to exec command: `%s`, output : %s', $command, implode($output)));
        }

        return $output;
    }

    /**
     * The actual dump function.
     */
    private function dump(): void
    {
        $dumpCommand = $this->buildDumpCommand($this->dumpFile);
        $dumpCommand .= ' 2>&1';
        $this->exec($dumpCommand);
    }

    private function dumpAllTables(): void
    {
        foreach ($this->listTables() as $table) {
            $this->dumpTable($table);
        }
    }

    /**
     * Lists table names (including prefix) in the current database, in a dialect-agnostic way.
     *
     * @return string[]
     */
    private function listTables(): array
    {
        /* @phpstan-ignore-next-line */
        $rows = $this->db->executeS(_DB_TYPE_ == 'pgsql'
            ? "SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'"
            : 'SHOW TABLES;');

        return array_map(static fn (array $row) => array_values($row)[0], $rows);
    }

    private function dumpTable(string $table): void
    {
        $tableDumpFile = $this->getTableDumpPath($table);
        $dumpCommand = $this->buildDumpCommand($tableDumpFile, $table);
        $dumpCommand .= ' 2>&1';
        $this->exec($dumpCommand);

        $checksum = $this->getTableChecksum($table);
        $checksumFile = $this->getTableChecksumPath($table);
        file_put_contents($checksumFile, $checksum);
    }

    private function getTableDumpPath(string $table): string
    {
        return sprintf(
            '%s/ps_dump_%s_%s_%s.sql',
            sys_get_temp_dir(),
            $this->databaseName,
            Version::VERSION,
            $table
        );
    }

    private function getTableChecksumPath(string $table): string
    {
        return sprintf(
            '%s/ps_dump_%s_%s_%s.md5',
            sys_get_temp_dir(),
            $this->databaseName,
            Version::VERSION,
            $table
        );
    }

    /**
     * Get checksum of the table to compare if the conent has been modified and needs to be restored. Since the checksum
     * doesn't take the auto increment index into consideration we fetch it manually and append it to the original
     * checksum, this allows to restore the index when needed as well.
     *
     * @param string $table
     *
     * @return string
     */
    private function getTableChecksum(string $table): string
    {
        /* @phpstan-ignore-next-line */
        if (_DB_TYPE_ == 'pgsql') {
            return $this->getPgsqlTableChecksum($table);
        }

        $checksum = $this->db->executeS(sprintf('CHECKSUM TABLE `%s`;', $table));
        $checksum = $checksum[0]['Checksum'];

        // The content only is not enough we must make sure that the auto increment index is the same
        $autoIncrement = $this->db->executeS(sprintf(
            'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = "%s" AND TABLE_NAME = "%s";',
            $this->databaseName,
            $table
        ));
        // Some tables have no auto increment (like relation tables for example)
        $autoIncrement = (int) ($autoIncrement[0]['AUTO_INCREMENT'] ?? 0);

        return $checksum . $autoIncrement;
    }

    /**
     * PostgreSQL has no CHECKSUM TABLE/information_schema AUTO_INCREMENT equivalent: hashes the
     * full row content instead (order-independent, via sorting on each row's own hash), and
     * appends the current value of the table's serial/identity sequence, if it has one.
     */
    private function getPgsqlTableChecksum(string $table): string
    {
        $checksumRow = $this->db->executeS(sprintf(
            'SELECT MD5(COALESCE(STRING_AGG(md5(t.*::text), \'\' ORDER BY md5(t.*::text)), \'\')) AS checksum FROM %s t',
            Db::quoteIdentifier($table)
        ));
        $checksum = $checksumRow[0]['checksum'];

        $sequenceRow = $this->db->executeS(sprintf(
            "SELECT s.relname FROM pg_class t
            JOIN pg_depend d ON d.refobjid = t.oid AND d.deptype IN ('a', 'i')
            JOIN pg_class s ON s.oid = d.objid AND s.relkind = 'S'
            WHERE t.relname = '%s' LIMIT 1",
            pSQL($table)
        ));

        $lastValue = 0;
        if (!empty($sequenceRow[0]['relname'])) {
            $lastValueRow = $this->db->executeS(sprintf(
                "SELECT last_value FROM pg_sequences WHERE schemaname = 'public' AND sequencename = '%s'",
                pSQL($sequenceRow[0]['relname'])
            ));
            $lastValue = (int) ($lastValueRow[0]['last_value'] ?? 0);
        }

        return $checksum . $lastValue;
    }

    private function checkDumpFile(): void
    {
        if (!file_exists($this->dumpFile)) {
            throw new Exception('You need to run \'composer create-test-db\' to create the initial test database');
        }
    }

    private function checkTableDumpFile(string $tableName): void
    {
        $dumpFile = $this->getTableDumpPath($tableName);
        if (!file_exists($dumpFile)) {
            throw new Exception(sprintf(
                'Cannot find dump for table %s, you need to run \'composer create-test-db\' to create the initial test database',
                $tableName
            ));
        }
    }

    /**
     * Make a database dump.
     */
    public static function create(): void
    {
        $dump = new static();

        $dump->dump();
    }

    /**
     * Make dump for each table in the database.
     */
    public static function dumpTables(): void
    {
        $dump = new static();

        $dump->dumpAllTables();
    }

    /**
     * Check that dump file exists
     *
     * @throws Exception
     */
    public static function checkDump(): void
    {
        $dump = new static();

        $dump->checkDumpFile();
    }

    /**
     * Restore a database dump.
     */
    public static function restoreDb(): void
    {
        $dump = new static();

        $dump->restore();
    }

    /**
     * Restore all tables (only modified tables are restored)
     */
    public static function restoreAllTables(): void
    {
        $dump = new static();

        foreach ($dump->listTables() as $tableName) {
            $tableName = substr($tableName, strlen($dump->dbPrefix));
            $dump->restoreTable($tableName);
        }
    }

    /**
     * Restore a list of tables in the database
     *
     * @param array $tableNames
     */
    public static function restoreTables(array $tableNames): void
    {
        $dump = new static();

        foreach ($tableNames as $tableName) {
            $dump->restoreTable($tableName);
        }
    }

    /**
     * Restore a list of tables in the database which name match the regexp
     *
     * @param string $regexp
     */
    public static function restoreMatchingTables(string $regexp): void
    {
        $dump = new static();

        foreach ($dump->listTables() as $tableName) {
            $tableName = substr($tableName, strlen($dump->dbPrefix));
            if (preg_match($regexp, $tableName)) {
                $dump->restoreTable($tableName);
            }
        }
    }

    public static function removeExtraTables(): void
    {
        $dump = new static();

        foreach ($dump->listTables() as $tableName) {
            // Remove all tables that contain _extra, they are the dynamically created tables used
            // by extra property feature, except extra_property_definition which is in the default structure
            // and must be kept
            if ($tableName !== $dump->dbPrefix . 'extra_property_definition' && str_contains($tableName, '_extra')) {
                $dump->db->execute('DROP TABLE ' . Db::quoteIdentifier($tableName) . ';');
            }
        }
    }
}
