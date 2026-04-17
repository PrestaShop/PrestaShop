<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
