<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

use PrestaShop\PrestaShop\Core\Grid\Position\UpdateHandler\PositionUpdateHandlerInterface;

/**
 * Class GridPositionUpdater, this class is responsible for updating the position of items
 * of a grid using the information from a PositionUpdateInterface object.
 */
final class GridPositionUpdater implements GridPositionUpdaterInterface
{
    /**
     * @var PositionUpdateHandlerInterface
     */
    private $updateHandler;

    /**
     * @param PositionUpdateHandlerInterface $updateHandler
     */
    public function __construct(PositionUpdateHandlerInterface $updateHandler)
    {
        $this->updateHandler = $updateHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function update(PositionUpdateInterface $positionUpdate)
    {
        $newPositions = $this->getNewPositions($positionUpdate);
        $this->sortByPositionValue($newPositions);
        $this->updateHandler->updatePositions($positionUpdate->getPositionDefinition(), $newPositions);
    }

    /**
     * @param PositionUpdateInterface $positionUpdate
     *
     * @return array
     */
    private function getNewPositions(PositionUpdateInterface $positionUpdate)
    {
        $positions = $this->updateHandler->getCurrentPositions($positionUpdate->getPositionDefinition(), $positionUpdate->getParentId());

        /** @var PositionModificationInterface $rowModification */
        foreach ($positionUpdate->getPositionModificationCollection() as $rowModification) {
            $positions[$rowModification->getId()] = $rowModification->getNewPosition();
        }

        return $positions;
    }

    /**
     * @param $positions
     */
    private function sortByPositionValue(&$positions)
    {
        asort($positions);
    }
}
