<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk;

use Iterator;

/**
 * Interface BulkActionCollectionInterface defines bulk actions contract.
 */
interface BulkActionCollectionInterface extends Iterator
{
    /**
     * Add bulk action to collection.
     *
     * @param BulkActionInterface $bulkAction
     *
     * @return self
     */
    public function add(BulkActionInterface $bulkAction);

    /**
     * Get bulk actions as array.
     *
     * @return array
     */
    public function toArray();
}
