<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
