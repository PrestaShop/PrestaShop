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

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use Countable;
use IteratorAggregate;
use PrestaShop\PrestaShop\Core\Import\File\DataCell\DataCellInterface;
use ArrayAccess;

/**
 * Interface DataRowInterface describes a data row from imported file
 */
interface DataRowInterface extends ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Add a cell to this row
     *
     * @param DataCellInterface $cell
     *
     * @return self
     */
    public function addCell(DataCellInterface $cell);

    /**
     * Create a data row from given array
     *
     * @param array $data
     *
     * @return self
     */
    public static function createFromArray(array $data);
}
