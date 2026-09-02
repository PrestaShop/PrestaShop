<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
