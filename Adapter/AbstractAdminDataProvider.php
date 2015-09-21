<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;
use PrestaShop\PrestaShop\Adapter\Product\AdminProductDataProvider;

/**
 * Base class for data provider, to give common Adapter functions.
 */
abstract class AbstractAdminDataProvider
{
    final private function compileSqlWhere($whereArray)
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
     * @see AdminProductDataProvider::getCatalogProductList() for an example.
     *
     * @param array $select
     * @param array $table
     * @param array $where
     * @param array $order
     * @param string $limit
     * @throws DevelopmentErrorException if SQL elements cannot be joined.
     * @return string The SQL query ready to be executed.
     */
    protected function compileSqlQuery(array $select, array $table, array $where, array $order, $limit = null)
    {
        $sql = array();

        // SELECT
        $s = array();
        foreach ($select as $alias => $field) {
            $a = is_string($alias)? ' AS `'.$alias.'`' : '';
            if (is_array($field)) {
                $s[] = ' '.$field['table'].'.`'.$field['field'].'` '.$a;
            } else {
                $s[] = ' '.$field.$a;
            }
        }
        if (count($s) === 0) {
            throw new DevelopmentErrorException('Compile SQL failed: No field to SELECT!');
        }
        $sql[] = 'SELECT SQL_CALC_FOUND_ROWS'.implode(','.PHP_EOL, $s);

        // FROM / JOIN
        $s = array();
        foreach ($table as $alias => $join) {
            if (!is_array($join)) {
                if (count($s) > 0) {
                    throw new DevelopmentErrorException('Compile SQL failed: cannot join the table '.$join.' into SQL query without JOIN sepcs.');
                }
                $s[0] = ' `'._DB_PREFIX_.$join.'` '.$alias;
            } else {
                if (count($s) === 0) {
                    throw new DevelopmentErrorException('Compile SQL failed: cannot join the table alias '.$alias.' into SQL query before to insert initial table.');
                }
                $s[] = ' '.$join['join'].' `'._DB_PREFIX_.$join['table'].'` '.$alias.((isset($join['on']))?' ON ('.$join['on'].')':'');
            }
        }
        if (count($s) === 0) {
            throw new DevelopmentErrorException('Compile SQL failed: No table to insert into FROM!');
        }
        $sql[] = 'FROM '.implode(' '.PHP_EOL, $s);

        // WHERE (recursive call)
        if (count($where)) {
            $s = $this->compileSqlWhere($where);
            if (strlen($s) > 0) {
                $sql[] = 'WHERE '.$s.PHP_EOL;
            }
        }

        // ORDER
        if (count($order) > 0) {
            $sql[] = 'ORDER BY '.implode(', ', $order).PHP_EOL;
        }

        // LIMIT
        if ($limit) {
            $sql[] = 'LIMIT '.$limit.PHP_EOL;
        }

        return implode(' '.PHP_EOL, $sql).';';
    }
}
