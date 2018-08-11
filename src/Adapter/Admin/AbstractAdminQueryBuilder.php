<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Admin;

use PrestaShop\PrestaShop\Adapter\Validate;
use Symfony\Component\Process\Exception\LogicException;

/**
 * Base class for data provider, to give common Adapter functions.
 *
 * Contains methods to compile SQL from parseable arrays of select, tables, joins, where, etc...
 */
abstract class AbstractAdminQueryBuilder
{
    const FILTERING_LIKE_BOTH = 'LIKE \'%%%s%%\'';
    const FILTERING_LIKE_LEFT = 'LIKE \'%%%s\'';
    const FILTERING_LIKE_RIGHT = 'LIKE \'%s%%\'';
    const FILTERING_EQUAL_NUMERIC = '= %s';
    const FILTERING_EQUAL_STRING = '= \'%s\'';

    private $lastCompiledSql = null;

    final private function compileSqlWhere(array $whereArray)
    {
        $operator = 'AND';
        $s = array();
        while ($item = array_shift($whereArray)) {
            if ($item == 'OR') {
                $operator = 'OR';
            } elseif ($item == 'AND') {
                $operator = 'AND';
            } else {
                $s[] = (is_array($item)? $this->compileSqlWhere($item) : $item);
            }
        }
        if (count($s) == 1) {
            return $s[0];
        }
        return '('.implode(' '.$operator.' ', $s).')';
    }

    /**
     * Compiles a SQL query (SELECT), from a group of associative arrays.
     *
     * @see PrestaShop\PrestaShop\Adapter\Product\AdminProductDataProvider::getCatalogProductList() for an example.
     *
     * Format example for $table:
     *   $table = array(
     *      'p' => 'product',                 // First table: a simple name
     *      'pl' => array(                    // Next: arrays to set join properly
     *          'table' => 'product_lang',
     *          'join' => 'LEFT JOIN',
     *          'on' => 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.$idLang.' AND pl.`id_shop` = '.$idShop
     *      ),
     *      'sav' => array(
     *          'table' => 'stock_available',
     *          'join' => 'LEFT JOIN',
     *          'on' => 'sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop_group = 1 AND sav.id_shop = 0'
     *      ),
     *      ...
     *   );
     *
     * Format example for $select:
     *   $select = array(
     *      'id_product' => array('table' => 'p', 'field' => 'id_product', 'filtering' => self::FILTERING_EQUAL_NUMERIC),
     *      'reference' => array('table' => 'p', 'field' => 'reference', 'filtering' => self::FILTERING_LIKE_BOTH),
     *      ...
     *   );
     *
     * Format example for $where:
     *   $where = array(
     *       'AND',                      // optional if AND, mandatory if OR.
     *       1,                          // First condition: let 1 here if there is no condition, then "WHERE 1;" will work better than "WHERE ;"
     *       array('OR', '2', '3'),
     *       array(
     *           'AND',
     *           array('OR', '4', '5'),
     *           array('6', '7')
     *       )
     *   );
     * In the WHERE, it's up to you to build each condition string. You can use the 'filtering' data in the $select array to help you:
     * $where[] = $select[$field]['table'].'.`'.$select[$field]['field'].'` '.sprintf($select[$field]['filtering'], $filterValue);
     *
     * Format example for $order:
     * $order = array('name ASC', 'id_product DESC');
     *
     * @param array[array[mixed]] $select
     * @param array[mixed] $table
     * @param array[mixed] $where
     * @param array[string] $groupBy
     * @param array[string] $order
     * @param string $limit
     * @throws LogicException if SQL elements cannot be joined.
     * @return string The SQL query ready to be executed.
     */
    protected function compileSqlQuery(array $select, array $table, array $where = array(), array $groupBy = array(), array $order = array(), $limit = null)
    {
        $sql = array();

        // SELECT
        $s = array();
        foreach ($select as $alias => $field) {
            $a = is_string($alias)? ' AS `'.$alias.'`' : '';
            if (is_array($field)) {
                if (isset($field['table'])) {
                    $s[] = ' '.$field['table'].'.`'.$field['field'].'` '.$a;
                } elseif (isset($field['select'])) {
                    $s[] = ' '.$field['select'].$a;
                }
            } else {
                $s[] = ' '.$field.$a;
            }
        }
        if (count($s) === 0) {
            throw new LogicException('Compile SQL failed: No field to SELECT!');
        }
        $sql[] = 'SELECT SQL_CALC_FOUND_ROWS'.implode(','.PHP_EOL, $s);

        // FROM / JOIN
        $s = array();
        foreach ($table as $alias => $join) {
            if (!is_array($join)) {
                if (count($s) > 0) {
                    throw new LogicException('Compile SQL failed: cannot join the table '.$join.' into SQL query without JOIN sepcs.');
                }
                $s[0] = ' `'._DB_PREFIX_.$join.'` '.$alias;
            } else {
                if (count($s) === 0) {
                    throw new LogicException('Compile SQL failed: cannot join the table alias '.$alias.' into SQL query before to insert initial table.');
                }
                $s[] = ' '.$join['join'].' `'._DB_PREFIX_.$join['table'].'` '.$alias.((isset($join['on']))?' ON ('.$join['on'].')':'');
            }
        }
        if (count($s) === 0) {
            throw new LogicException('Compile SQL failed: No table to insert into FROM!');
        }
        $sql[] = 'FROM '.implode(' '.PHP_EOL, $s);

        // WHERE (recursive call)
        if (count($where)) {
            $s = $this->compileSqlWhere($where);
            if (strlen($s) > 0) {
                $sql[] = 'WHERE '.$s.PHP_EOL;
            }
        }

        // GROUP BY
        if (!empty($groupBy)) {
            $sql[] = 'GROUP BY '. implode(', ', array_map('pSQL', $groupBy)) . PHP_EOL;
        }

        // ORDER
        if (count($order) > 0) {
            $goodOrder = array();
            foreach ($order as $o) {
                $value = explode(' ', $o);
                if (!empty($value) && 2 === count($value) && Validate::isOrderBy($value[0]) && Validate::isOrderWay($value[1])) {
                    $goodOrder[] = ' `'.bqSQL($value[0]).'` '. $value[1];
                }
            }

            if (count($goodOrder) > 0) {
                $sql[] = 'ORDER BY ' . implode(', ', $goodOrder) . PHP_EOL;
            }
        }

        // LIMIT
        if ($limit) {
            $sql[] = 'LIMIT '.$limit.PHP_EOL;
        }

        $this->lastCompiledSql = implode(' '.PHP_EOL, $sql).';';
        return $this->lastCompiledSql;
    }

    /**
     * Returns the last SQL query that was compiled on this Provider.
     *
     * @return string The last SQL query that was compiled with $this->compileSqlQuery()
     */
    public function getLastCompiledSql()
    {
        return $this->lastCompiledSql;
    }
}
