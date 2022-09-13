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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;

class StockMovementRepository
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
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param StockId $stockId
     * @param int $offset
     * @param int $limit
     *
     * @return array<array<string, mixed>>
     */
    public function getLastEmployeeStockMovements(StockId $stockId, int $offset = 0, int $limit = 3): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('sm.*')
            ->from($this->dbPrefix . 'stock_mvt', 'sm')
            ->where('id_stock = :stockId')
            ->setParameter('stockId', $stockId->getValue())
            ->addOrderBy('sm.date_add', 'DESC')
            ->addOrderBy('sm.id_stock_mvt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->execute()->fetchAllAssociative();
    }
}
