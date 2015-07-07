<?php

/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Helper;

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
     * Constructor extracts database connection info from PrestaShop's confifugation,
     * but we use mysqldump and mysql for dump / restore.
     */
    private function __construct()
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
        $this->user = _DB_USER_;
        $this->password = _DB_PASSWD_;
    }

    /**
     * Clean the temporary file.
     */
    public function __destruct()
    {
        if ($this->dumpFile && file_exists($this->dumpFile)) {
            unlink($this->dumpFile);
            $this->dumpFile = null;
        }
    }

    /**
     * Wrapper to easily build mysql commands: sets password, port, user
     */
    private function buildMySQLCommand($executable, array $arguments = array())
    {
        $parts = array(
            escapeshellarg($executable),
            '-u', escapeshellarg($this->user),
            '-P', escapeshellarg($this->port),
            '-h', escapeshellarg($this->host),
        );

        if ($this->password) {
            $parts[] = '-p'.escapeshellarg($this->password);
        }

        $parts = array_merge($parts, array_map('escapeshellarg', $arguments));

        return implode(' ', $parts);
    }

    /**
     * Like exec, but will raise an exception if the command failed.
     */
    private function exec($command)
    {
        $output = array();
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
        $dumpCommand = $this->buildMySQLCommand('mysqldump', array($this->databaseName));
        $this->dumpFile = tempnam(sys_get_temp_dir(), 'ps_dump');
        $dumpCommand .= ' > ' . escapeshellarg($this->dumpFile);
        $this->exec($dumpCommand);
    }

    /**
     * Restore the dump to the actual database.
     */
    public function restore()
    {
        $restoreCommand = $this->buildMySQLCommand('mysql', array($this->databaseName));
        $restoreCommand .= ' < ' . escapeshellarg($this->dumpFile);
        $this->exec($restoreCommand);
    }

    /**
     * Make a database dump and return an object on which you can call `restore` to restore the dump.
     */
    public static function create()
    {
        $dump = new DatabaseDump();

        $dump->dump();

        return $dump;
    }
}
