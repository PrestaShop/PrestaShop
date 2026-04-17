<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row;

use Iterator;

/**
 * Interface RowActionCollectionInterface defines contract for row actions collection.
 */
interface RowActionCollectionInterface extends Iterator
{
    /**
     * Add row action to collection.
     *
     * @param RowActionInterface $action
     *
     * @return self
     */
    public function add(RowActionInterface $action);
}
