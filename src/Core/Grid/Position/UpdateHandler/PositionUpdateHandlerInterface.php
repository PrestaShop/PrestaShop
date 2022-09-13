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

namespace PrestaShop\PrestaShop\Core\Grid\Position\UpdateHandler;

use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinitionInterface;

/**
 * Interface PositionUpdateHandlerInterface is used by GridPositionUpdater to
 * manipulate the data, handling the manipulation in this interface allows the
 * GridPositionUpdater to adapt more easily to different databases or any other
 * persistence solutions.
 */
interface PositionUpdateHandlerInterface
{
    /**
     * Returns the complete list of positions based on the PositionDefinitionInterface
     * The expected return is an associative array with row IDs used as keys and positions
     * as values.
     *
     * ex: $currentPositions = [
     *      1 => 0,
     *      4 => 1,
     *      42 => 2,
     *      3 => 3
     * ];
     *
     * @param PositionDefinitionInterface $positionDefinition
     * @param string|int $parentId
     *
     * @return array
     */
    public function getCurrentPositions(PositionDefinitionInterface $positionDefinition, $parentId = null);

    /**
     * This method is used to update the new positions previously fetched through getCurrentPositions which
     * have been updated by the GridPositionUpdater, hence the $newPositions has the same format as the one
     * returned by getCurrentPositions, except of course the positions are likely to have changed.
     *
     * ex: $newPositions = [
     *      1 => 3,
     *      4 => 1,
     *      42 => 2,
     *      3 => 3
     * ];
     *
     * Throws a PositionUpdateException if something went wrong.
     *
     * @param PositionDefinitionInterface $positionDefinition
     * @param array $newPositions
     * @param string|int $parentId
     *
     * @throws PositionUpdateException
     */
    public function updatePositions(PositionDefinitionInterface $positionDefinition, array $newPositions, $parentId = null);
}
