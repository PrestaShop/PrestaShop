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

namespace PrestaShop\PrestaShop\Adapter\Carrier\Repository;

use Carrier;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use IteratorAggregate;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierRange;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierRangePrice;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Provides access to carrier range data source
 */
class CarrierRangeRepository
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
        protected readonly CarrierRepository $carrierRepository,
    ) {
    }

    /**
     * @param CarrierId $carrierId
     *
     * @return CarrierRange[]
     *
     * @throws CarrierException
     * @throws \Doctrine\DBAL\Exception
     * @throws \PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException
     * @throws CarrierConstraintException
     * @throws \PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException
     * @throws \PrestaShop\PrestaShop\Core\Exception\CoreException
     */
    public function get(CarrierId $carrierId, ShopConstraint $shopConstraint): array
    {
        // Check shop constraint
        $this->assertShopConstraint($shopConstraint);

        // Get carrier
        $carrier = $this->carrierRepository->get($carrierId);

        // Then, create query to retrieve carrier ranges
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('cd.id_zone, cr.delimiter1 AS range_from, cr.delimiter2 AS range_to, cd.price AS range_price')
            ->from($this->dbPrefix . 'delivery', 'cd')
            ->andWhere('cd.id_carrier = :carrierId')
            ->setParameter('carrierId', $carrierId->getValue());

        // Apply shipping method (weight or price) and shop constraint, then execute the query
        $this->applyRangeTypeForQuery($qb, $carrier);
        $this->applyShopConstraint($qb, $shopConstraint);
        $results = $qb->executeQuery()->fetchAllAssociative();

        // Format results with CarrierRange and CarrierRangePrice objets
        return self::formatRangesFromData($results);
    }

    /**
     * @param CarrierId $carrierId
     * @param array $ranges
     * @param ShopConstraint $shopConstraint
     *
     * @return CarrierId
     */
    public function set(CarrierId $carrierId, array $ranges, ShopConstraint $shopConstraint): CarrierId
    {
        // Check shop constraint
        $this->assertShopConstraint($shopConstraint);

        // Get carrier
        $carrier = $this->carrierRepository->get($carrierId);
        $rangeTable = $this->getRangeTypeTable($carrier);

        // Use transaction to ensure data consistency
        $this->connection->beginTransaction();

        // Reset carrier ranges
        $this->reset($carrierId, $shopConstraint);

        // Foreach range
        foreach ($ranges as $range) {
            // Insert range
            $this->connection->insert(
                $this->dbPrefix . $rangeTable,
                [
                    'id_carrier' => $carrierId->getValue(),
                    'delimiter1' => $range->getFrom(),
                    'delimiter2' => $range->getTo(),
                ]
            );
            $rangeId = $this->connection->lastInsertId();

            // Foreach prices in range
            foreach ($range->getPrices() as $price) {
                // Insert price
                $this->connection->insert(
                    $this->dbPrefix . 'delivery',
                    [
                        'id_carrier' => $carrierId->getValue(),
                        'id_' . $rangeTable => $rangeId,
                        'id_zone' => $price->getZoneId(),
                        'price' => $price->getPrice(),
                    ]
                );
            }
        }

        // Commit transaction
        $this->connection->commit();

        return $carrierId;
    }

    /**
     * Format ranges with CarrierRange and CarrierRangePrice objects from data
     *
     * @param IteratorAggregate|array $iterator
     *
     * @return CarrierRange[]
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException
     */
    public static function formatRangesFromData(IteratorAggregate|array $iterator): array
    {
        $ranges = $prices = [];
        $tmpLast = ['from' => null, 'to' => null];
        foreach ($iterator as $range) {
            if ($tmpLast['from'] !== $range['range_from'] || $tmpLast['to'] !== $range['range_to']) {
                if (count($prices) > 0) {
                    $ranges[] = new CarrierRange(
                        (float) $tmpLast['from'],
                        (float) $tmpLast['to'],
                        $prices
                    );
                    $prices = [];
                }
                $tmpLast = ['from' => $range['range_from'], 'to' => $range['range_to']];
            }

            $prices[] = new CarrierRangePrice(
                (int) $range['id_zone'],
                (float) $range['range_price']
            );
        }

        $ranges[] = new CarrierRange(
            (float) $tmpLast['from'],
            (float) $tmpLast['to'],
            $prices
        );

        return $ranges;
    }

    private function reset(CarrierId $carrierId, ShopConstraint $shopConstraint): void
    {
        $this->assertShopConstraint($shopConstraint);
        $tablesToReset = [
            'range_weight',
            'range_price',
            'delivery',
        ];

        foreach ($tablesToReset as $table) {
            $this->connection->delete(
                $this->dbPrefix . $table,
                ['id_carrier' => $carrierId->getValue()]
            );
        }
    }

    private function getRangeTypeTable(Carrier $carrier): string
    {
        switch ($carrier->shipping_method) {
            case Carrier::SHIPPING_METHOD_WEIGHT:
                return 'range_weight';
            case Carrier::SHIPPING_METHOD_PRICE:
                return 'range_price';
            default:
                throw new CarrierException(sprintf('Unknown shipping method "%s"', $carrier->shipping_method));
        }
    }

    private function applyRangeTypeForQuery(QueryBuilder $queryBuilder, Carrier $carrier): QueryBuilder
    {
        // Define which table to join based on carrier shipping method
        $tableRange = $this->getRangeTypeTable($carrier);

        // Join the range table and order by range
        $queryBuilder->innerJoin(
            'cd',
            $this->dbPrefix . $tableRange,
            'cr',
            'cd.id_' . $tableRange . ' = cr.id_' . $tableRange . ' AND cr.id_carrier = :carrierId'
        )
            ->orderBy('cr.id_' . $tableRange, 'ASC');

        return $queryBuilder;
    }

    private function applyShopConstraint(QueryBuilder $queryBuilder, ShopConstraint $shopConstraint): QueryBuilder
    {
        // todo: make shop constraint magic here!
        return $queryBuilder;
    }

    private function assertShopConstraint(ShopConstraint $shopConstraint): void
    {
        if (!$shopConstraint->forAllShops()) {
            throw new CarrierConstraintException(
                'Shop constraint isn\'t supported yet.',
                CarrierConstraintException::INVALID_SHOP_CONSTRAINT
            );
        }
    }
}
