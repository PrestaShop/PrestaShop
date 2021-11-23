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

namespace PrestaShopBundle\Install;

use AppKernel;
use Exception;

class DatabaseDump
{
    private $host;
    private $port;
    private $user;
    private $password;
    private $databaseName;
    private $dumpFile;

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

        if ($dumpFile === null) {
            $this->dumpFile = sprintf('%s/ps_dump_%s.sql', sys_get_temp_dir(), AppKernel::VERSION);
        } else {
            $this->dumpFile = $dumpFile;
        }
        $this->databaseName = _DB_NAME_;
        $this->user = _DB_USER_;
        $this->password = _DB_PASSWD_;
    }

    /**
     * Wrapper to easily build mysql commands: sets password, port, user.
     *
     * @param string $executable
     * @param array $arguments
     *
     * @return string
     */
    private function buildMySQLCommand($executable, array $arguments = [])
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
    private function exec($command)
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
    private function dump()
    {
        $dumpCommand = $this->buildMySQLCommand('mysqldump', [$this->databaseName]);
        $dumpCommand .= ' > ' . escapeshellarg($this->dumpFile) . ' 2> /dev/null';
        $this->exec($dumpCommand);
    }

    /**
     * Restore the dump to the actual database.
     */
    public function restore()
    {
        $restoreCommand = $this->buildMySQLCommand('mysql', [$this->databaseName]);
        $restoreCommand .= ' < ' . escapeshellarg($this->dumpFile) . ' 2> /dev/null';
        $this->exec($restoreCommand);
    }

    /**
     * Make a database dump.
     */
    public static function create()
    {
        $dump = new static();

        $dump->dump();
    }

    /**
     * Restore a database dump.
     */
    public static function restoreDb()
    {
        $dump = new static();

        $dump->restore();
    }
}
