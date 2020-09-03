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
                        $ret .= ' AND ' . $tableAlias . '`' . bqSQL($sqlId) . '` BETWEEN "' . pSQL($matches3[0]) . '" AND "' . pSQL($matches3[1]) . "\"\n";
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
