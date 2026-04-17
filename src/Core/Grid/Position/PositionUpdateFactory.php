<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                (int) $position[$this->rowIdField],
                isset($position[$this->oldPositionField]) ? (int) $position[$this->oldPositionField] : null,
                (int) $position[$this->newPositionField]
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
        if (!isset($position[$this->newPositionField])) {
            throw new PositionDataException(self::POSITION_KEY, 'Admin.Notifications.Failure', [$index, $this->newPositionField]);
        }
    }
}
