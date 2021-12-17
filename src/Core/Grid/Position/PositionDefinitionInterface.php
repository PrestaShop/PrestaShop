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

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Interface PositionDefinitionInterface used to define a position relationship,
 * contains information about the database storing the position.
 */
interface PositionDefinitionInterface
{
    /**
     * The name of the table containing the position.
     *
     * @return string
     */
    public function getTable();

    /**
     * The name of the ID field in the row containing position.
     *
     * @return string
     */
    public function getIdField();

    /**
     * The name of the position field in the row containing position.
     *
     * @return string
     */
    public function getPositionField();

    /**
     * The name of the parent ID field  in the row containing position, it
     * is used to compute the positions in the parent scope.
     * It is optional as the position may be bound to the table scope only.
     *
     * @return string|null
     */
    public function getParentIdField();

    /**
     * Which value should be used for the first position (can be 0, 1 or anything else)
     *
     * @return int
     */
    public function getFirstPosition(): int;
}
