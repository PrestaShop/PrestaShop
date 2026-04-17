<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Interface PositionUpdateInterface contains the modifications needed
 * to update the grid positions.
 */
interface PositionUpdateInterface
{
    /**
     * The PositionDefinition defines the position relationship and
     * allows to be build the database request.
     *
     * @return PositionDefinitionInterface
     */
    public function getPositionDefinition();

    /**
     * A collection of modifications for each modified rows.
     *
     * @return PositionModificationCollectionInterface
     */
    public function getPositionModificationCollection();

    /**
     * If the PositionDefinition needs a parent and has defined a
     * parentIdField then this field contains its value.
     *
     * @return string|null
     */
    public function getParentId();
}
