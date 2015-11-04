<?php

namespace PrestaShop\PrestaShop\Core\Foundation\Database;

use Core_Foundation_Database_DatabaseInterface;
use Doctrine\DBAL\Connection;

class AutoPrefixingDatabase implements Core_Foundation_Database_DatabaseInterface
{
    private $prefix;
    private $db;

    public function __construct(
        $prefix,
        Connection $db
    ) {
        $this->prefix = $prefix;
        $this->db     = $db;
    }

    public function addPrefix($sql)
    {
        return str_replace('prefix_', $this->prefix, $sql);
    }

    public function select($sql, array $params = [])
    {
        $stmt = $this->db->executeQuery(
            $this->addPrefix($sql),
            $params
        );
        return $stmt->fetchAll();
    }

    public function getValue($sql)
    {
        $rows = $this->select($sql);
        return current(current($rows));
    }

    public function escape($value)
    {
        return $this->db->quote($value);
    }
}
