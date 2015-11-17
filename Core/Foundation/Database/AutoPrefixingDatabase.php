<?php

namespace PrestaShop\PrestaShop\Core\Foundation\Database;

use Core_Business_ConfigurationInterface;
use Core_Foundation_Database_DatabaseInterface;

class AutoPrefixingDatabase implements Core_Foundation_Database_DatabaseInterface
{
    private $configuration;
    private $db;

    public function __construct(
        Core_Business_ConfigurationInterface $configuration,
        Core_Foundation_Database_DatabaseInterface $db
    ) {
        $this->configuration    = $configuration;
        $this->db               = $db;
    }

    public function addPrefix($sql)
    {
        return str_replace('prefix_', $this->configuration->get('_DB_PREFIX_'), $sql);
    }

    public function select($sql)
    {
        return $this->db->select($this->addPrefix($sql));
    }

    public function getValue($sql)
    {
        $rows = $this->select($sql);
        return current(current($rows));
    }

    public function escape($value)
    {
        return $this->db->escape($value);
    }
}
