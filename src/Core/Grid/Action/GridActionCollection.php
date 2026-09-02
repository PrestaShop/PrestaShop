<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;

/**
 * Class PanelActionCollection is responsible for holding single grid actions.
 *
 * @property GridActionInterface[] $items
 */
final class GridActionCollection extends AbstractCollection implements GridActionCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(GridActionInterface $action)
    {
        $this->items[$action->getId()] = $action;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $actionsArray = [];

        foreach ($this->items as $action) {
            $actionsArray[] = [
                'id' => $action->getId(),
                'name' => $action->getName(),
                'icon' => $action->getIcon(),
                'type' => $action->getType(),
                'options' => $action->getOptions(),
            ];
        }

        return $actionsArray;
    }
}
