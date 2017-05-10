<?php
/**
 * 2007-2017 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
            if ($this->shouldCastToInt($columnName)) {
                $columnValue = (int)$columnValue;
            }
        };

        array_walk($rows, function (&$rowColumns) use ($castIdentifiersToIntegers) {
            array_walk($rowColumns, $castIdentifiersToIntegers);
        });

        return $rows;
    }

    /**
     * @param $columnName
     * @return bool
     */
    private function shouldCastToInt($columnName)
    {
        return false !== strpos($columnName, '_id') ||
        false !== strpos($columnName, '_quantity') ||
        false !== strpos($columnName, 'total_');
    }
}
