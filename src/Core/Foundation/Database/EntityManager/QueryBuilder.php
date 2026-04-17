<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager;

use PrestaShop\PrestaShop\Core\Foundation\Database\Exception;

class QueryBuilder
{
    private $db;

    public function __construct(\PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface $db)
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

        $parts = [];

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
