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
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierRangesCollection;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

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
     * @return array<int, array<int|string>>
     *
     * @throws CarrierException
     * @throws Exception
     * @throws AttributeNotFoundException
     * @throws CarrierConstraintException
     * @throws ZoneException
     * @throws CoreException
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
        $this->applyShopConstraint($qb);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param CarrierId $carrierId
     * @param CarrierRangesCollection $rangesCollection
     * @param ShopConstraint $shopConstraint
     */
    public function set(CarrierId $carrierId, CarrierRangesCollection $rangesCollection, ShopConstraint $shopConstraint): void
    {
        // Check shop constraint
        $this->assertShopConstraint($shopConstraint);

        // Get carrier
        $carrier = $this->carrierRepository->get($carrierId);
        $rangeTable = $this->getRangeMethodTable((int) $carrier->shipping_method);

        // Use transaction to ensure data consistency
        $this->connection->beginTransaction();

        // Reset carrier ranges
        $this->reset($carrierId, $shopConstraint);

        // Foreach zones
        $rangesIds = [];
        foreach ($rangesCollection->getZones() as $zone) {
            $idZone = $zone->getZoneId();

            // Foreach ranges in current zone
            foreach ($zone->getRanges() as $range) {
                // To avoid duplicated content in database, we will use a unique key for each range based on from-to values
                $rangeKey = $range->getFrom() . '-' . $range->getTo();
                // Check if range already exist in database
                if (!in_array($rangeKey, array_keys($rangesIds), true)) {
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
                    $rangesIds[$rangeKey] = $rangeId;
                } else {
                    $rangeId = $rangesIds[$rangeKey];
                }

                // Insert price in delivery table
                $this->connection->insert(
                    $this->dbPrefix . 'delivery',
                    [
                        'id_carrier' => $carrierId->getValue(),
                        'id_' . $rangeTable => $rangeId,
                        'id_zone' => $idZone,
                        'price' => $range->getPrice(),
                        // Only all shops is handled for now
                        'id_shop' => null,
                        'id_shop_group' => null,
                    ]
                );
            }
        }

        // Commit transaction
        $this->connection->commit();
    }

    /**
     * @throws Exception
     * @throws CarrierConstraintException
     */
    private function reset(CarrierId $carrierId, ShopConstraint $shopConstraint): void
    {
        $this->assertShopConstraint($shopConstraint);

        // First, we reset delivery
        $this->connection->delete(
            $this->dbPrefix . 'delivery',
            [
                'id_carrier' => $carrierId->getValue(),
                // Only all shops is handled for now
                'id_shop' => null,
                'id_shop_group' => null,
            ]
        );

        // Then, we delete ranges if they are not used anymore for price and weight calculation
        foreach (['range_weight', 'range_price'] as $rangeTable) {
            $qb = $this->connection->createQueryBuilder();
            $qb->delete($this->dbPrefix . $rangeTable)
                ->where('id_carrier = :carrierId')
                ->andWhere(
                    'id_' . $rangeTable . ' NOT IN
                    (SELECT id_' . $rangeTable . ' FROM ' . $this->dbPrefix . 'delivery WHERE id_carrier = :carrierId)'
                )
                ->setParameter('carrierId', $carrierId->getValue())
                ->executeQuery();
        }
    }

    private function getRangeMethodTable(int $calculatingMethod): string
    {
        switch ($calculatingMethod) {
            case ShippingMethod::BY_WEIGHT:
                return 'range_weight';
            case ShippingMethod::BY_PRICE:
                return 'range_price';
            default:
                throw new CarrierException(sprintf('Unknown shipping method "%s"', $calculatingMethod));
        }
    }

    private function applyRangeTypeForQuery(QueryBuilder $queryBuilder, Carrier $carrier): QueryBuilder
    {
        // Define which table to join based on carrier shipping method
        $tableRange = $this->getRangeMethodTable((int) $carrier->shipping_method);

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

    private function applyShopConstraint(QueryBuilder $queryBuilder): QueryBuilder
    {
        // We need temporary to force where id_shop and id_shop_group are null for retro compatibility
        $queryBuilder->andWhere('cd.id_shop IS NULL AND cd.id_shop_group IS NULL');

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
