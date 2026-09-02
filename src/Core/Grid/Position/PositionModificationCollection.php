<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;

/**
 * Class PositionModificationCollection holds collection of row modifications for grid.
 *
 * @property PositionModificationInterface[] $items
 */
final class PositionModificationCollection extends AbstractCollection implements PositionModificationCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(PositionModificationInterface $positionModification)
    {
        $this->items[$positionModification->getId()] = $positionModification;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(PositionModificationInterface $positionModification)
    {
        if (isset($this->items[$positionModification->getId()])) {
            unset($this->items[$positionModification->getId()]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $positionModifications = [];

        /** @var PositionModificationInterface $item */
        foreach ($this->items as $item) {
            $positionModifications[] = [
                'id' => $item->getId(),
                'oldPosition' => $item->getOldPosition(),
                'newPosition' => $item->getNewPosition(),
            ];
        }

        return $positionModifications;
    }
}
