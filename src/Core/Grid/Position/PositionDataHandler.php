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

use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;

/**
 * Class PositionDataHandler is a basic implementation of the PositionDataHandlerInterface,
 * it transforms the provided array data into a PositionUpdate object.
 * Of course, you need to respect a data format for it to work but if you don't want to use
 * this format you are free to implement your own interface.
 */
class PositionDataHandler implements PositionDataHandlerInterface
{
    /**
     * Transform the provided data into a PositionUpdate. You need to provided the data
     * into a specific format:
     *
     * $data = [
     *      'positions' => [
     *          [
     *              'rowId' => 1,
     *              'oldPosition' => 1,
     *              'newPosition' => 1,
     *          ],
     *          [
     *              'rowId' => 2,
     *              'oldPosition' => 2,
     *              'newPosition' => 3,
     *          ],
     *          [
     *              'rowId' => 3,
     *              'oldPosition' => 3,
     *              'newPosition' => 2,
     *          ],
     *      ],
     *      'parentId' => 42,
     * ];
     *
     * @param array $data
     * @param PositionDefinition $positionDefinition
     *
     * @return PositionUpdate
     * @throws PositionDataException
     */
    public function handleData(array $data, PositionDefinition $positionDefinition)
    {
        $this->validateData($data, $positionDefinition);

        $updates = new PositionModificationCollection();
        foreach ($data['positions'] as $position) {
            $this->validatePositionData($position);

            $updates->add(new PositionModification(
                $position['rowId'],
                $position['oldPosition'],
                $position['newPosition']
            ));
        }

        $positionUpdate = new PositionUpdate(
            $updates,
            $positionDefinition,
            isset($data['parentId']) ? $data['parentId'] : null
        );

        return $positionUpdate;
    }

    /**
     * @param array $data
     * @param PositionDefinition $positionDefinition
     * @throws PositionDataException
     */
    private function validateData(array $data, PositionDefinition $positionDefinition)
    {
        if (empty($data['positions'])) {
            throw new PositionDataException(
                'Missing positions in your data.',
                'Admin.Notifications.Failure',
                []
            );
        }

        if (null !== $positionDefinition->getParentIdField() && empty($data['parentId'])) {
            throw new PositionDataException(
                'Missing parentId in your data.',
                'Admin.Notifications.Failure',
                []
            );
        }
    }

    /**
     * Validate the position format, throw a PositionDataException if is not correct.
     * @param array $position
     * @throws PositionDataException
     */
    private function validatePositionData(array $position)
    {
        if (!isset($position['rowId'])) {
            throw new PositionDataException(
                'Invalid position data, missing rowId field.',
                'Admin.Notifications.Failure',
                []
            );
        }
        if (!isset($position['oldPosition'])) {
            throw new PositionDataException(
                'Invalid position data, missing oldPosition field.',
                'Admin.Notifications.Failure',
                []
            );
        }
        if (!isset($position['newPosition'])) {
            throw new PositionDataException(
                'Invalid position data, missing newPosition field.',
                'Admin.Notifications.Failure',
                []
            );
        }
    }
}
