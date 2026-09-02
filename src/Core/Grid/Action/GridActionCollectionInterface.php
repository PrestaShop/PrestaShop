<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action;

use Iterator;

/**
 * Interface GridActionCollectionInterface defines contract for grid action collection.
 */
interface GridActionCollectionInterface extends Iterator
{
    /**
     * Add grid action to collection.
     *
     * @param GridActionInterface $action
     *
     * @return self
     */
    public function add(GridActionInterface $action);

    /**
     * Get grid panel actions as array.
     *
     * @return array
     */
    public function toArray();
}
