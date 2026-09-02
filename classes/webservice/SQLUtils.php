<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

class SQLUtils
{
    /**
     * @param string $sqlId
     * @param string $filterValue
     * @param string $tableAlias = 'main.'
     *
     * @return string
     */
    public static function getSQLRetrieveFilter($sqlId, $filterValue, $tableAlias = 'main.')
    {
        if (!empty($tableAlias)) {
            $tableAlias = '`' . bqSQL(str_replace('.', '', $tableAlias)) . '`.';
        }

        $ret = '';
        preg_match('/^(.*)\[(.*)\](.*)$/', $filterValue, $matches);
        if (count($matches) > 1) {
            if ($matches[1] == '%' || $matches[3] == '%') {
                $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '` LIKE "' . pSQL($matches[1] . $matches[2] . $matches[3]) . "\"\n";
            } elseif ($matches[1] == '' && $matches[3] == '') {
                if (strpos($matches[2], '|') > 0) {
                    $values = explode('|', $matches[2]);
                    $ret .= ' AND (';
                    $temp = '';
                    foreach ($values as $value) {
                        $temp .= $tableAlias . '`' . bqSQL($sqlId) . '` = "' . bqSQL($value) . '" OR ';
                    }
                    $ret .= rtrim($temp, 'OR ') . ')' . "\n";
                } elseif (preg_match('/^([\d\.:\-\s]+),([\d\.:\-\s]+)$/', $matches[2], $matches3)) {
                    unset($matches3[0]);
                    if (count($matches3) > 0) {
                        sort($matches3);
                        [$first, $last] = array_values($matches3); // reset-keys
                        $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '` BETWEEN "' . pSQL($first) . '" AND "' . pSQL($last) . "\"\n";
                    }
                } else {
                    $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '`="' . pSQL($matches[2]) . '"' . "\n";
                }
            } elseif ($matches[1] == '>') {
                $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '` > "' . pSQL($matches[2]) . "\"\n";
            } elseif ($matches[1] == '<') {
                $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '` < "' . pSQL($matches[2]) . "\"\n";
            } elseif ($matches[1] == '!') {
                $multiple_values = explode('|', $matches[2]);
                foreach ($multiple_values as $value) {
                    $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '` != "' . pSQL($value) . "\"\n";
                }
            }
        } else {
            $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '` = "' . pSQL($filterValue) . "\"\n";
        }

        return $ret;
    }
}
