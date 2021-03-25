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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Position\UpdateHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Statement;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinitionInterface;

/**
 * Class DoctrinePositionMultiStoreUpdateHandler updates the grid positions using a Doctrine
 * Connection.
 */
final class DoctrinePositionMultiStoreUpdateHandler implements PositionUpdateHandlerInterface
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
     * @var int
     */
    private $shopId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $shopId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        int $shopId
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPositions(PositionDefinitionInterface $positionDefinition, $parentId = null)
    {
        if (null != $positionDefinition->getShopId()) {
            $this->shopId = $positionDefinition->getShopId();
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from($this->dbPrefix . $positionDefinition->getTable() . '_shop', 't')
            ->select('t.' . $positionDefinition->getIdField() . ', t.' . $positionDefinition->getPositionField())
            ->andWhere('t.id_shop = :shopId')
            ->setParameter('shopId', $this->shopId)
            ->addOrderBy('t.' . $positionDefinition->getPositionField(), 'ASC');

        if (null !== $parentId && null !== $positionDefinition->getParentIdField()) {
            $qb
                ->andWhere('tr.' . $positionDefinition->getParentIdField() . ' = :parentId')
                ->leftJoin(
                    't',
                    $this->dbPrefix . $positionDefinition->getTable(),
                    'tr',
                    'tr.' . $positionDefinition->getIdField() . ' = t.' . $positionDefinition->getIdField()
                )
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
    public function updatePositions(PositionDefinitionInterface $positionDefinition, array $newPositions)
    {
        try {
            $this->connection->beginTransaction();

            if (null != $positionDefinition->getShopId()) {
                $this->shopId = $positionDefinition->getShopId();
            }

            $positionIndex = 0;
            foreach ($newPositions as $rowId => $newPosition) {
                $qb = $this->connection->createQueryBuilder();
                $qb
                    ->update($this->dbPrefix . $positionDefinition->getTable() . '_shop')
                    ->set($positionDefinition->getPositionField(), ':position')
                    ->andWhere($positionDefinition->getIdField() . ' = :rowId')
                    ->andWhere('id_shop = :shopId')
                    ->setParameter('shopId', $this->shopId)
                    ->setParameter('rowId', $rowId)
                    ->setParameter('position', $positionIndex);

                $statement = $qb->execute();
                if ($statement instanceof Statement && $statement->errorCode()) {
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
