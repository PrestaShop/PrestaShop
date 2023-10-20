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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            $this->port = 3306;
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

        $restoreCommand = $this->buildMySQLCommand('mysql', [$this->databaseName]);
        $restoreCommand .= ' < ' . escapeshellarg($this->dumpFile) . ' 2> /dev/null';
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
        $restoreCommand = $this->buildMySQLCommand('mysql', [$this->databaseName]);
        $restoreCommand .= ' < ' . escapeshellarg($dumpFile) . ' 2> /dev/null';
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
     * Wrapper to easily build mysql commands: sets password, port, user.
     *
     * @param string $executable
     * @param array $arguments
     *
     * @return string
     */
    private function buildMySQLCommand($executable, array $arguments = []): string
    {
        $parts = [
            escapeshellarg($executable),
            '-u', escapeshellarg($this->user),
            '-P', escapeshellarg($this->port),
            '-h', escapeshellarg($this->host),
        ];

        if ($this->password) {
            $parts[] = '-p' . escapeshellarg($this->password);
        }

        $parts = array_merge($parts, array_map('escapeshellarg', $arguments));

        return implode(' ', $parts);
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
            throw new Exception(sprintf('Unable to exec command: `%s`, missing a binary?', $command));
        }

        return $output;
    }

    /**
     * The actual dump function.
     */
    private function dump(): void
    {
        $dumpCommand = $this->buildMySQLCommand('mysqldump', [$this->databaseName]);
        $dumpCommand .= ' > ' . escapeshellarg($this->dumpFile) . ' 2> /dev/null';
        $this->exec($dumpCommand);
    }

    private function dumpAllTables(): void
    {
        $tables = $this->db->executeS('SHOW TABLES;');
        foreach ($tables as $table) {
            // $table is an array looking like this [Tables_in_database_name => 'ps_access']
            $this->dumpTable(reset($table));
        }
    }

    private function dumpTable(string $table): void
    {
        $dumpCommand = $this->buildMySQLCommand('mysqldump', [$this->databaseName, $table]);
        $tableDumpFile = $this->getTableDumpPath($table);
        $dumpCommand .= ' > ' . escapeshellarg($tableDumpFile) . ' 2> /dev/null';
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
        $checksum = $this->db->executeS(sprintf('CHECKSUM TABLE %s;', $table));
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

        $tables = $dump->db->executeS('SHOW TABLES;');
        foreach ($tables as $table) {
            // $table is an array looking like this [Tables_in_database_name => 'ps_access']
            $tableName = reset($table);
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

        $tables = $dump->db->executeS('SHOW TABLES;');
        foreach ($tables as $table) {
            // $table is an array looking like this [Tables_in_database_name => 'ps_access']
            $tableName = reset($table);
            $tableName = substr($tableName, strlen($dump->dbPrefix));
            if (preg_match($regexp, $tableName)) {
                $dump->restoreTable($tableName);
            }
        }
    }
}
