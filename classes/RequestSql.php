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

/**
 * Class RequestSqlCore.
 */
class RequestSqlCore extends ObjectModel
{
    public $name;
    public $sql;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'request_sql',
        'primary' => 'id_request_sql',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 200],
            'sql' => ['type' => self::TYPE_SQL, 'validate' => 'isString', 'required' => true, 'size' => 4194303],
        ],
    ];

    /** @var array : List of params to tested */
    public $tested = [
        'required' => ['SELECT', 'FROM'],
        'option' => ['WHERE', 'ORDER', 'LIMIT', 'HAVING', 'GROUP', 'UNION'],
        'operator' => [
            'AND', '&&', 'BETWEEN', 'AND', 'BINARY', '&', '~', '|', '^', 'CASE', 'WHEN', 'END', 'DIV', '/', '<=>', '=', '>=',
            '>', 'IS', 'NOT', 'NULL', '<<', '<=', '<', 'LIKE', '-', '%', '!=', '<>', 'REGEXP', '!', '||', 'OR', '+', '>>', 'RLIKE', 'SOUNDS', '*',
            '-', 'XOR', 'IN',
        ],
        'function' => [
            'AVG', 'SUM', 'COUNT', 'MIN', 'MAX', 'STDDEV', 'STDDEV_SAMP', 'STDDEV_POP', 'VARIANCE', 'VAR_SAMP', 'VAR_POP',
            'GROUP_CONCAT', 'BIT_AND', 'BIT_OR', 'BIT_XOR',
        ],
        'unauthorized' => [
            'DELETE', 'ALTER', 'INSERT', 'REPLACE', 'CREATE', 'TRUNCATE', 'OPTIMIZE', 'GRANT', 'REVOKE', 'SHOW', 'HANDLER',
            'LOAD', 'LOAD_FILE', 'ROLLBACK', 'SAVEPOINT', 'UNLOCK', 'INSTALL', 'UNINSTALL', 'ANALZYE', 'BACKUP', 'CHECK', 'CHECKSUM', 'REPAIR', 'RESTORE', 'CACHE',
            'DESCRIBE', 'EXPLAIN', 'USE', 'HELP', 'SET', 'DUPLICATE', 'VALUES',  'INTO', 'RENAME', 'CALL', 'PROCEDURE',  'FUNCTION', 'DATABASE', 'SERVER',
            'LOGFILE', 'DEFINER', 'RETURNS', 'EVENT', 'TABLESPACE', 'VIEW', 'TRIGGER', 'DATA', 'DO', 'PASSWORD', 'USER', 'PLUGIN', 'FLUSH', 'KILL',
            'RESET', 'START', 'STOP', 'PURGE', 'EXECUTE', 'PREPARE', 'DEALLOCATE', 'LOCK', 'USING', 'DROP', 'FOR', 'UPDATE', 'BEGIN', 'BY', 'ALL', 'SHARE',
            'MODE', 'TO', 'KEY', 'DISTINCTROW', 'DISTINCT',  'HIGH_PRIORITY', 'LOW_PRIORITY', 'DELAYED', 'IGNORE', 'FORCE', 'STRAIGHT_JOIN',
            'SQL_SMALL_RESULT', 'SQL_BIG_RESULT', 'QUICK', 'SQL_BUFFER_RESULT', 'SQL_CACHE', 'SQL_NO_CACHE', 'SQL_CALC_FOUND_ROWS', 'WITH',
            'OUTFILE', 'DUMPFILE',
        ],
    ];

    public $attributes = [
        'passwd' => '*******************',
        'secure_key' => '*******************',
    ];

    /** @var array : list of errors */
    public $error_sql = [];

    /**
     * Get list of request SQL.
     *
     * @return array|bool
     */
    public static function getRequestSql()
    {
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'request_sql` ORDER BY `id_request_sql`')) {
            return false;
        }

        $requestSql = [];
        foreach ($result as $row) {
            $requestSql[] = $row['sql'];
        }

        return $requestSql;
    }

    /**
     * Get list of request SQL by id request.
     *
     * @param int $id
     *
     * @return array
     */
    public static function getRequestSqlById($id)
    {
        return Db::getInstance()->executeS('SELECT `sql` FROM `' . _DB_PREFIX_ . 'request_sql` WHERE `id_request_sql` = ' . (int) $id);
    }

    /**
     * Call the parserSQL() method in Tools class
     * Cut the request in table for check it.
     *
     * @param string $sql
     *
     * @return array|bool
     */
    public function parsingSql($sql)
    {
        return Tools::parserSQL($sql);
    }

    /**
     * Check if the parsing of the SQL request is good or not.
     *
     * @param array $tab
     * @param bool $in
     * @param string $sql
     *
     * @return bool
     */
    public function validateParser($tab, $in, $sql)
    {
        if (!$tab) {
            return false;
        } elseif (isset($tab['UNION'])) {
            $union = $tab['UNION'];
            foreach ($union as $tab) {
                if (!$this->validateSql($tab, $in, $sql)) {
                    return false;
                }
            }

            return true;
        } else {
            return $this->validateSql($tab, $in, $sql);
        }
    }

    /**
     * Cut the request for check each cutting.
     *
     * @param array<string, array> $tab
     * @param bool $in
     * @param string $sql
     *
     * @return bool
     */
    public function validateSql($tab, $in, $sql)
    {
        if (!$this->testedRequired($tab)) {
            return false;
        } elseif (!$this->testedUnauthorized($tab)) {
            return false;
        } elseif (!$this->checkedFrom($tab['FROM'])) {
            return false;
        } elseif (!$this->checkedSelect($tab['SELECT'], $tab['FROM'], $in)) {
            return false;
        } elseif (isset($tab['WHERE'])) {
            if (!$this->checkedWhere($tab['WHERE'], $tab['FROM'], $sql)) {
                return false;
            }
        } elseif (isset($tab['HAVING'])) {
            if (!$this->checkedHaving($tab['HAVING'], $tab['FROM'])) {
                return false;
            }
        } elseif (isset($tab['ORDER'])) {
            if (!$this->checkedOrder($tab['ORDER'], $tab['FROM'])) {
                return false;
            }
        } elseif (isset($tab['GROUP'])) {
            if (!$this->checkedGroupBy($tab['GROUP'], $tab['FROM'])) {
                return false;
            }
        } elseif (isset($tab['LIMIT'])) {
            if (!$this->checkedLimit($tab['LIMIT'])) {
                return false;
            }
        }

        if (empty($this->_errors) && !Db::getInstance()->executeS($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get list of all tables.
     *
     * @return array
     */
    public function getTables()
    {
        $results = Db::getInstance()->executeS('SHOW TABLES');
        $tables = [];
        foreach ($results as $result) {
            $key = array_keys($result);
            $tables[] = $result[$key[0]];
        }

        return $tables;
    }

    /**
     * Get list of all attributes by an table.
     *
     * @param string $table
     *
     * @return array
     */
    public function getAttributesByTable($table)
    {
        return Db::getInstance()->executeS('DESCRIBE ' . pSQL($table));
    }

    /**
     * Cut an join sentence.
     *
     * @param array $attrs
     * @param array $from
     *
     * @return array
     */
    public function cutJoin($attrs, $from)
    {
        $tab = [];

        foreach ($attrs as $attr) {
            if (in_array($attr['expr_type'], ['operator', 'const'])) {
                continue;
            }

            if (!empty($attr['sub_tree'])) {
                foreach ($attr['sub_tree'] as $treeItem) {
                    if ($treeItem['expr_type'] !== 'colref') {
                        continue;
                    }
                    if ($attribut = $this->cutAttribute($treeItem['base_expr'], $from)) {
                        $tab[] = $attribut;
                    }
                }
            } else {
                if ($attribut = $this->cutAttribute($attr['base_expr'], $from)) {
                    $tab[] = $attribut;
                }
            }
        }

        return $tab;
    }

    /**
     * Cut an attribute with or without the alias.
     *
     * @param string $attr
     * @param array $from
     *
     * @return array|bool
     */
    public function cutAttribute($attr, $from)
    {
        $matches = [];
        if (preg_match('/((`(\()?([a-z0-9_])+`(\))?)|((\()?([a-z0-9_])+(\))?))\.((`(\()?([a-z0-9_])+`(\))?)|((\()?([a-z0-9_])+(\))?))$/i', $attr, $matches, PREG_OFFSET_CAPTURE)) {
            $tab = explode('.', str_replace(['`', '(', ')'], '', $matches[0][0]));
            if ($table = $this->returnNameTable($tab[0], $from)) {
                return [
                    'table' => $table,
                    'alias' => $tab[0],
                    'attribut' => $tab[1],
                    'string' => $attr,
                ];
            }
        } elseif (preg_match('/((`(\()?([a-z0-9_])+`(\))?)|((\()?([a-z0-9_])+(\))?))$/i', $attr, $matches, PREG_OFFSET_CAPTURE)) {
            $attribut = str_replace(['`', '(', ')'], '', $matches[0][0]);
            if ($table = $this->returnNameTable(false, $from, $attr)) {
                return [
                    'table' => $table,
                    'attribut' => $attribut,
                    'string' => $attr,
                ];
            }
        }

        return false;
    }

    /**
     * Get name of table by alias.
     *
     * @param string|false $alias
     * @param array $tables
     *
     * @return array|bool
     */
    public function returnNameTable($alias, $tables, $attr = null)
    {
        if ($alias) {
            foreach ($tables as $table) {
                if (!isset($table['alias']) || !isset($table['table'])) {
                    continue;
                }
                /** @var string|array{'parts': array<int, bool>} $tableAlias */
                $tableAlias = $table['alias']['no_quotes'];
                if ($tableAlias == $alias || $tableAlias['parts'][0] == $alias) {
                    return [$table['table']];
                }
            }
        } elseif (count($tables) > 1) {
            if ($attr !== null) {
                $tab = [];
                foreach ($tables as $table) {
                    if ($this->attributExistInTable($attr, $table['table'])) {
                        $tab = $table['table'];
                    }
                }
                if (count($tab) == 1) {
                    return $tab;
                }
            }

            $this->error_sql['returnNameTable'] = false;

            return false;
        }

        $tab = [];
        foreach ($tables as $table) {
            $tab[] = $table['table'];
        }

        return $tab;
    }

    /**
     * Check if an attributes exists in a table.
     *
     * @param string $attr
     * @param array $table
     *
     * @return bool
     */
    public function attributExistInTable($attr, $table)
    {
        if (!$attr) {
            return true;
        }
        if (is_array($table) && (count($table) == 1)) {
            $table = $table[0];
        }
        $attributs = $this->getAttributesByTable($table);
        foreach ($attributs as $attribut) {
            if ($attribut['Field'] == trim($attr, ' `')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if all required sentence existing.
     *
     * @param array $tab
     *
     * @return bool
     */
    public function testedRequired($tab)
    {
        foreach ($this->tested['required'] as $key) {
            if (!array_key_exists($key, $tab)) {
                $this->error_sql['testedRequired'] = $key;

                return false;
            }
        }

        return true;
    }

    /**
     * Check if an unauthorized existing in an array.
     *
     * @param array $tab
     *
     * @return bool
     */
    public function testedUnauthorized($tab)
    {
        foreach ($this->tested['unauthorized'] as $key) {
            if (array_key_exists($key, $tab)) {
                $this->error_sql['testedUnauthorized'] = $key;

                return false;
            }
        }

        return true;
    }

    /**
     * Check a "FROM" sentence.
     *
     * @param array<int, array<string, mixed>> $from
     *
     * @return bool
     */
    public function checkedFrom($from)
    {
        $nb = count($from);
        for ($i = 0; $i < $nb; ++$i) {
            $table = $from[$i];

            if (isset($table['table']) && !in_array(str_replace('`', '', $table['table']), $this->getTables())) {
                $this->error_sql['checkedFrom']['table'] = $table['table'];

                return false;
            }
            if ($table['ref_type'] == 'ON' && (trim($table['join_type']) == 'LEFT' || trim($table['join_type']) == 'JOIN')) {
                $attrs = $this->cutJoin($table['ref_clause'], $from);
                foreach ($attrs as $attr) {
                    if (!$this->attributExistInTable($attr['attribut'], $attr['table'])) {
                        $this->error_sql['checkedFrom']['attribut'] = [$attr['attribut'], implode(', ', $attr['table'])];

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Check a "SELECT" sentence.
     *
     * @param array<int, array<string, mixed>> $select
     * @param array $from
     * @param bool $in
     *
     * @return bool
     */
    public function checkedSelect($select, $from, $in = false)
    {
        $nb = count($select);
        for ($i = 0; $i < $nb; ++$i) {
            $attribut = $select[$i];
            if ($attribut['base_expr'] != '*' && !preg_match('/\.\*$/', $attribut['base_expr'])) {
                if ($attribut['expr_type'] == 'colref') {
                    if ($attr = $this->cutAttribute(trim($attribut['base_expr']), $from)) {
                        if (!$this->attributExistInTable($attr['attribut'], $attr['table'])) {
                            $this->error_sql['checkedSelect']['attribut'] = [$attr['attribut'], implode(', ', $attr['table'])];

                            return false;
                        }
                    } else {
                        if (isset($this->error_sql['returnNameTable'])) {
                            $this->error_sql['checkedSelect'] = $this->error_sql['returnNameTable'];

                            return false;
                        } else {
                            $this->error_sql['checkedSelect'] = false;

                            return false;
                        }
                    }
                }

                while (is_array($attribut['sub_tree'])) {
                    if ($attribut['expr_type'] === 'function' && in_array(strtoupper($attribut['base_expr']), $this->tested['unauthorized'])) {
                        $this->error_sql['checkedSelect']['function'] = $attribut['base_expr'];

                        return false;
                    }
                    $attribut = $attribut['sub_tree'][0];
                }
            } elseif ($in) {
                $this->error_sql['checkedSelect']['*'] = false;

                return false;
            }
        }

        return true;
    }

    /**
     * Check a "WHERE" sentence.
     *
     * @param array<int, array<string, mixed>> $where
     * @param array $from
     * @param string $sql
     *
     * @return bool
     */
    public function checkedWhere($where, $from, $sql)
    {
        $nb = count($where);
        for ($i = 0; $i < $nb; ++$i) {
            $attribut = $where[$i];
            if ($attribut['expr_type'] == 'colref') {
                if ($attr = $this->cutAttribute(trim($attribut['base_expr']), $from)) {
                    if (!$this->attributExistInTable($attr['attribut'], $attr['table'])) {
                        $this->error_sql['checkedWhere']['attribut'] = [$attr['attribut'], implode(', ', $attr['table'])];

                        return false;
                    }
                } else {
                    $this->error_sql['checkedWhere'] = $this->error_sql['returnNameTable'] ?? false;

                    return false;
                }
            } elseif ($attribut['expr_type'] == 'reserved') {
                if ($attribut['base_expr'] !== 'EXISTS' || !isset($where[$i + 1]) || $where[$i + 1]['expr_type'] !== 'subquery') {
                    $this->error_sql['checkedWhere'] = $this->error_sql['returnNameTable'] ?? false;

                    return false;
                }
            } elseif ($attribut['expr_type'] == 'operator') {
                if (!in_array(strtoupper($attribut['base_expr']), $this->tested['operator'])) {
                    $this->error_sql['checkedWhere']['operator'] = [$attribut['base_expr']];

                    return false;
                }
            } elseif ($attribut['expr_type'] == 'subquery') {
                $tab = $attribut['sub_tree'];

                return $this->validateParser($tab, true, $sql);
            }
        }

        return true;
    }

    /**
     * Check a "HAVING" sentence.
     *
     * @param array<int, array<string, mixed>> $having
     * @param array $from
     *
     * @return bool
     */
    public function checkedHaving($having, $from)
    {
        $nb = count($having);
        for ($i = 0; $i < $nb; ++$i) {
            $attribut = $having[$i];
            if ($attribut['expr_type'] == 'colref') {
                if ($attr = $this->cutAttribute(trim($attribut['base_expr']), $from)) {
                    if (!$this->attributExistInTable($attr['attribut'], $attr['table'])) {
                        $this->error_sql['checkedHaving']['attribut'] = [$attr['attribut'], implode(', ', $attr['table'])];

                        return false;
                    }
                } else {
                    if (isset($this->error_sql['returnNameTable'])) {
                        $this->error_sql['checkedHaving'] = $this->error_sql['returnNameTable'];

                        return false;
                    } else {
                        $this->error_sql['checkedHaving'] = false;

                        return false;
                    }
                }
            }

            if ($attribut['expr_type'] == 'operator') {
                if (!in_array(strtoupper($attribut['base_expr']), $this->tested['operator'])) {
                    $this->error_sql['checkedHaving']['operator'] = [$attribut['base_expr']];

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check a "ORDER" sentence.
     *
     * @param array $order
     * @param array $from
     *
     * @return bool
     */
    public function checkedOrder($order, $from)
    {
        $order = $order[0];
        if (array_key_exists('expression', $order) && $order['type'] == 'expression') {
            if ($attr = $this->cutAttribute(trim($order['base_expr']), $from)) {
                if (!$this->attributExistInTable($attr['attribut'], $attr['table'])) {
                    $this->error_sql['checkedOrder']['attribut'] = [$attr['attribut'], implode(', ', $attr['table'])];

                    return false;
                }
            } else {
                if (isset($this->error_sql['returnNameTable'])) {
                    $this->error_sql['checkedOrder'] = $this->error_sql['returnNameTable'];

                    return false;
                } else {
                    $this->error_sql['checkedOrder'] = false;

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check a "GROUP BY" sentence.
     *
     * @param array $group
     * @param array $from
     *
     * @return bool
     */
    public function checkedGroupBy($group, $from)
    {
        $group = $group[0];
        if ($group['expr_type'] == 'colref') {
            if ($attr = $this->cutAttribute(trim($group['base_expr']), $from)) {
                if (!$this->attributExistInTable($attr['attribut'], $attr['table'])) {
                    $this->error_sql['checkedGroupBy']['attribut'] = [$attr['attribut'], implode(', ', $attr['table'])];

                    return false;
                }
            } else {
                if (isset($this->error_sql['returnNameTable'])) {
                    $this->error_sql['checkedGroupBy'] = $this->error_sql['returnNameTable'];

                    return false;
                } else {
                    $this->error_sql['checkedGroupBy'] = false;

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check a "LIMIT" sentence.
     *
     * @param array $limit
     *
     * @return bool
     */
    public function checkedLimit($limit)
    {
        if (!preg_match('#^[0-9]+$#', trim($limit['offset'])) || !preg_match('#^[0-9]+$#', trim($limit['rowcount']))) {
            $this->error_sql['checkedLimit'] = false;

            return false;
        }

        return true;
    }
}
