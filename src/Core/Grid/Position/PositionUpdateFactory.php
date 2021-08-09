<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;

/**
 * Class PositionUpdateFactory is a basic implementation of the PositionUpdateFactoryInterface,
 * it transforms the provided array data into a PositionUpdate object.
 */
final class PositionUpdateFactory implements PositionUpdateFactoryInterface
{
    public const POSITION_KEY = 'Invalid position %i data, missing %s field.';

    /**
     * @var string
     */
    private $positionsField;

    /**
     * @var string
     */
    private $rowIdField;

    /**
     * @var string
     */
    private $oldPositionField;

    /**
     * @var string
     */
    private $newPositionField;

    /**
     * @var string
     */
    private $parentIdField;

    /**
     * @param string $positionsField
     * @param string $rowIdField
     * @param string $oldPositionField
     * @param string $newPositionField
     * @param string $parentIdField
     */
    public function __construct(
        $positionsField,
        $rowIdField,
        $oldPositionField,
        $newPositionField,
        $parentIdField
    ) {
        $this->positionsField = $positionsField;
        $this->rowIdField = $rowIdField;
        $this->oldPositionField = $oldPositionField;
        $this->newPositionField = $newPositionField;
        $this->parentIdField = $parentIdField;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPositionUpdate(array $data, PositionDefinition $positionDefinition)
    {
        $this->validateData($data, $positionDefinition);

        $updates = new PositionModificationCollection();
        foreach ($data[$this->positionsField] as $index => $position) {
            $this->validatePositionData($position, $index);

            $updates->add(new PositionModification(
                $position[$this->rowIdField],
                $position[$this->oldPositionField],
                $position[$this->newPositionField]
            ));
        }

        $positionUpdate = new PositionUpdate(
            $updates,
            $positionDefinition,
            isset($data[$this->parentIdField]) ? $data[$this->parentIdField] : null
        );

        return $positionUpdate;
    }

    /**
     * @param array $data
     * @param PositionDefinition $positionDefinition
     *
     * @throws PositionDataException
     */
    private function validateData(array $data, PositionDefinition $positionDefinition)
    {
        if (empty($data[$this->positionsField])) {
            throw new PositionDataException('Missing ' . $this->positionsField . ' in your data.', 'Admin.Notifications.Failure');
        }

        if (null !== $positionDefinition->getParentIdField() && empty($data[$this->parentIdField])) {
            throw new PositionDataException('Missing ' . $this->parentIdField . ' in your data.', 'Admin.Notifications.Failure');
        }
    }

    /**
     * Validate the position format, throw a PositionDataException if is not correct.
     *
     * @param array $position
     * @param int $index
     *
     * @throws PositionDataException
     */
    private function validatePositionData(array $position, $index)
    {
        if (!isset($position[$this->rowIdField])) {
            throw new PositionDataException(self::POSITION_KEY, 'Admin.Notifications.Failure', [$index, $this->rowIdField]);
        }
        if (!isset($position[$this->oldPositionField])) {
            throw new PositionDataException(self::POSITION_KEY, 'Admin.Notifications.Failure', [$index, $this->oldPositionField]);
        }
        if (!isset($position[$this->newPositionField])) {
            throw new PositionDataException(self::POSITION_KEY, 'Admin.Notifications.Failure', [$index, $this->newPositionField]);
        }
    }
}
