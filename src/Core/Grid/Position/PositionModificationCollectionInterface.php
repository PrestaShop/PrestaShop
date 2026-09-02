<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

use Countable;
use Iterator;

/**
 * Interface PositionModificationCollectionInterface defines contract for grid RowModificationInterface collection.
 */
interface PositionModificationCollectionInterface extends Iterator, Countable
{
    /**
     * Add rowModification to collection.
     *
     * @param PositionModificationInterface $positionModification
     *
     * @return self
     */
    public function add(PositionModificationInterface $positionModification);

    /**
     * Remove positionModification from collection.
     *
     * @param PositionModificationInterface $positionModification
     *
     * @return self
     */
    public function remove(PositionModificationInterface $positionModification);

    /**
     * Get positionModifications as array.
     *
     * @return array
     */
    public function toArray();
}
