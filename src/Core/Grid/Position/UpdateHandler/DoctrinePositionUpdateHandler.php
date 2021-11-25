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

namespace PrestaShop\PrestaShop\Core\Grid\Position\UpdateHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Exception;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
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

        $positions = $qb->execute()->fetchAll();
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

                try {
                    $qb->execute();
                } catch (Exception $e) {
                    throw new PositionUpdateException('Could not update #%i', 'Admin.Catalog.Notification', [$rowId]);
                }
                ++$positionIndex;
            }
            $this->connection->commit();
        } catch (ConnectionException $e) {
            $this->connection->rollBack();

            throw new PositionUpdateException('Could not update.', 'Admin.Catalog.Notification');
        }
    }
}
