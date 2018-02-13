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

namespace PrestaShopBundle\Entity\Repository;

trait NormalizeFieldTrait
{
    /**
     * @param $rows
     * @return mixed
     */
    protected function castNumericToInt($rows)
    {
        $castIdentifiersToIntegers = function (&$columnValue, $columnName) {
            if ($this->shouldCastToInt($columnName, $columnValue)) {
                $columnValue = (int)$columnValue;
            }
        };

        array_walk_recursive($rows, $castIdentifiersToIntegers);

        return $rows;
    }

    /**
     * @param $rows
     * @return mixed
     */
    protected function castIdsToArray($rows)
    {
        $castIdentifiersToArray = function (&$columnValue, $columnName) {
            if ($this->shouldCastToInt($columnName, $columnValue)) {
                $columnValue = array_map('intval', explode(',', $columnValue));
            }
        };

        array_walk_recursive($rows, $castIdentifiersToArray);

        return $rows;
    }

    /**
     * @param String $columnName
     * @param String $columnValue
     *
     * @return bool
     */
    private function shouldCastToInt($columnName, $columnValue)
    {
        if (null === $columnValue || 'N/A' === $columnValue) {
            return false;
        }

        return preg_match('/_id|id_|_quantity|sign|active|total_|low_stock_/', $columnName);
    }
}
