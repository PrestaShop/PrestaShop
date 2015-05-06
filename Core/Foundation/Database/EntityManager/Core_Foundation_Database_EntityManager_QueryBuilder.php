<?php

class Core_Foundation_Database_EntityManager_QueryBuilder
{
    private $db;

    public function __construct(Core_Foundation_Database_Database $db)
    {
        $this->db = $db;
    }

    public function quote($value)
    {
        $escaped = $this->db->escape($value);

        if (is_string($value)) {
            return "'" . $escaped . "'";
        } else {
            return $escaped;
        }
    }

    public function buildWhereConditions($andOrOr, array $conditions)
    {
        $operator = strtoupper($andOrOr);

        if ($operator !== 'AND' && $operator !== 'OR') {
            throw new Exception(sprintf('Invalid operator %s - must be "and" or "or".', $andOrOr));
        }

        $parts = array();

        foreach ($conditions as $key => $value) {
            if (is_scalar($value)) {
                $parts[] = $key . ' = ' . $this->quote($value);
            } else {
                $list = [];
                foreach ($value as $item) {
                    $list[] = $this->quote($item);
                }
                $parts[] = $key . ' IN (' . implode(', ', $list) . ')';
            }
        }

        return implode(" $operator ", $parts);
    }
}
