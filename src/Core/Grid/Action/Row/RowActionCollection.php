<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;

/**
 * Class RowActionCollection defines contract for grid row action collection.
 */
final class RowActionCollection extends AbstractCollection implements RowActionCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(RowActionInterface $action)
    {
        $this->items[$action->getId()] = $action;

        return $this;
    }
}
