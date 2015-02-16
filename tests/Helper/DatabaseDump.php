<?php

namespace PrestaShop\PrestaShop\Tests\Helper;

class DatabaseDump
{
    private $host;
    private $port;
    private $user;
    private $password;
    private $databaseName;
    private $dumpFile;

    private function __construct()
    {
        $host_and_maybe_port = explode(':', _DB_SERVER_);

        if (count($host_and_maybe_port) === 1)
        {
            $this->host = $host_and_maybe_port[0];
            $this->port = 3306;
        }
        else if (count($host_and_maybe_port) === 2)
        {
            $this->host = $host_and_maybe_port[0];
            $this->port = $host_and_maybe_port[1];
        }

        $this->databaseName = _DB_NAME_;
        $this->user = _DB_USER_;
        $this->password = _DB_PASSWD_;
    }

    public function __destruct()
    {
        if ($this->dumpFile && file_exists($this->dumpFile))
        {
            unlink($this->dumpFile);
            $this->dumpFile = null;
        }
    }

    private function buildMySQLCommand($executable, array $arguments = array())
    {
        $parts = array(
            escapeshellarg($executable),
            '-u', escapeshellarg($this->user),
            '-P', escapeshellarg($this->port),
        );

        if ($this->password)
        {
            $parts[] = '-p';
            $parts[] = escapeshellarg($this->password);
        }

        $parts = array_merge($parts, array_map('escapeshellarg', $arguments));

        return implode(' ', $parts);
    }

    private function dump()
    {
        $dumpCommand = $this->buildMySQLCommand('mysqldump', array($this->databaseName));
        $this->dumpFile = tempnam(sys_get_temp_dir(), 'ps_dump');
        $dumpCommand .= ' > ' . escapeshellarg($this->dumpFile);
        exec($dumpCommand);
    }

    public function restore()
    {
        $restoreCommand = $this->buildMySQLCommand('mysql', array($this->databaseName));
        $restoreCommand .= ' < ' . escapeshellarg($this->dumpFile);
        exec($restoreCommand);
    }

    public static function create()
    {
        $dump = new DatabaseDump();

        $dump->dump();

        return $dump;
    }
}
