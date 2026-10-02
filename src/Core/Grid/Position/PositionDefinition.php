<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Class PositionDefinition used to define a position relationship, see
 * PositionDefinitionInterface for more details.
 */
final class PositionDefinition implements PositionDefinitionInterface
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $idField;

    /**
     * @var string
     */
    private $positionField;

    /**
     * @var string|null
     */
    private $parentIdField;

    /**
     * @var int
     */
    private $firstPosition;

    /**
     * @var array<string, scalar> column => value the rows taking part in the sequence must match
     */
    private $filters;

    /**
     * @param string $table
     * @param string $idField
     * @param string $positionField
     * @param string|null $parentIdField
     * @param int $firstPosition
     * @param array<string, scalar> $filters restricts the sequence to the rows the grid displays,
     *                                       for a table whose grid hides some of its rows
     */
    public function __construct(
        $table,
        $idField,
        $positionField,
        $parentIdField = null,
        int $firstPosition = 0,
        array $filters = []
    ) {
        $this->table = $table;
        $this->idField = $idField;
        $this->positionField = $positionField;
        $this->parentIdField = $parentIdField;
        $this->firstPosition = $firstPosition;
        $this->filters = $filters;
    }

    /**
     * Not on PositionDefinitionInterface on purpose: adding a method there would break any module
     * implementing it. The update handler asks for these only when the definition is this class.
     *
     * @return array<string, scalar>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionField()
    {
        return $this->positionField;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentIdField()
    {
        return $this->parentIdField;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPosition(): int
    {
        return $this->firstPosition;
    }
}
