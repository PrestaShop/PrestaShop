<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position\UpdateHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Exception;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinitionInterface;

/**
 * Class DoctrinePositionUpdateHandler updates the grid positions using a Doctrine
 * Connection.
 */
final class DoctrinePositionUpdateHandler implements PositionUpdateHandlerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPositions(PositionDefinitionInterface $positionDefinition, $parentId = null)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from($this->dbPrefix . $positionDefinition->getTable(), 't')
            ->select('t.' . $positionDefinition->getIdField() . ', t.' . $positionDefinition->getPositionField())
            ->addOrderBy('t.' . $positionDefinition->getPositionField(), 'ASC');

        if (null !== $parentId && null !== $positionDefinition->getParentIdField()) {
            $qb
                ->andWhere('t.`' . $positionDefinition->getParentIdField() . '` = :parentId')
                ->setParameter('parentId', $parentId);
        }

        foreach ($this->getFilters($positionDefinition) as $column => $value) {
            $qb
                ->andWhere('t.`' . $column . '` = :filter_' . $column)
                ->setParameter('filter_' . $column, $value);
        }

        $positions = $qb->executeQuery()->fetchAllAssociative();
        $currentPositions = [];
        foreach ($positions as $position) {
            $positionId = $position[$positionDefinition->getIdField()];
            $currentPositions[$positionId] = $position[$positionDefinition->getPositionField()];
        }

        return $currentPositions;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePositions(PositionDefinitionInterface $positionDefinition, array $newPositions, $parentId = null)
    {
        try {
            $this->connection->beginTransaction();
            $positionIndex = $positionDefinition->getFirstPosition();
            foreach ($newPositions as $rowId => $newPosition) {
                $qb = $this->connection->createQueryBuilder();
                $qb
                    ->update($this->dbPrefix . $positionDefinition->getTable())
                    ->set($positionDefinition->getPositionField(), ':position')
                    ->andWhere($positionDefinition->getIdField() . ' = :rowId')
                    ->setParameter('rowId', $rowId)
                    ->setParameter('position', $positionIndex);

                if (null !== $parentId && null !== $positionDefinition->getParentIdField()) {
                    $qb
                        ->andWhere('`' . $positionDefinition->getParentIdField() . '` = :parentId')
                        ->setParameter('parentId', $parentId);
                }

                foreach ($this->getFilters($positionDefinition) as $column => $value) {
                    $qb
                        ->andWhere('`' . $column . '` = :filter_' . $column)
                        ->setParameter('filter_' . $column, $value);
                }

                try {
                    $qb->executeStatement();
                } catch (Exception) {
                    throw new PositionUpdateException('Could not update #%i', 'Admin.Catalog.Notification', [$rowId]);
                }
                ++$positionIndex;
            }
            $this->connection->commit();
        } catch (ConnectionException) {
            $this->connection->rollBack();

            throw new PositionUpdateException('Could not update.', 'Admin.Catalog.Notification');
        }
    }

    /**
     * A grid that hides part of its table - carriers keep their superseded rows as `deleted` - must
     * not have those rows numbered, or they consume positions and the visible ones come out with
     * gaps. The filters live on PositionDefinition rather than on its interface so that a module
     * implementing that interface keeps working.
     *
     * @return array<string, scalar>
     */
    private function getFilters(PositionDefinitionInterface $positionDefinition): array
    {
        return $positionDefinition instanceof PositionDefinition ? $positionDefinition->getFilters() : [];
    }
}
