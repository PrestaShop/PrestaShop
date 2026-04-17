<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;

/**
 * Class BulkActionCollection holds bulk action collection available for grid.
 *
 * @property BulkActionInterface[] $items
 */
final class BulkActionCollection extends AbstractCollection implements BulkActionCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(BulkActionInterface $bulkAction)
    {
        $this->items[$bulkAction->getId()] = $bulkAction;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $bulkActionsArray = [];

        foreach ($this->items as $bulkAction) {
            $bulkActionsArray[] = [
                'id' => $bulkAction->getId(),
                'name' => $bulkAction->getName(),
                'type' => $bulkAction->getType(),
                'icon' => $bulkAction->getIcon(),
                'options' => $bulkAction->getOptions(),
            ];
        }

        return $bulkActionsArray;
    }
}
