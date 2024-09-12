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
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotAddCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotUpdateCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use RuntimeException;

/**
 * Provides access to carrier data source
 */
class CarrierRepository extends AbstractMultiShopObjectModelRepository
{
    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly Connection $connection,
        private readonly string $prefix,
    ) {
    }

    /**
     * @param CarrierId $carrierId
     *
     * @return Carrier
     *
     * @throws AttributeNotFoundException
     * @throws CoreException
     */
    public function get(CarrierId $carrierId): Carrier
    {
        /** @var Carrier $carrier */
        $carrier = $this->getObjectModel(
            $carrierId->getValue(),
            Carrier::class,
            CarrierNotFoundException::class
        );

        return $carrier;
    }

    public function add(Carrier $carrier, array $shopIds): CarrierId
    {
        $carrierId = $this->addObjectModelToShops(
            $carrier,
            $shopIds,
            CannotAddCarrierException::class
        );

        return new CarrierId((int) $carrierId);
    }

    public function update(Carrier $carrier, int $errorCode): void
    {
        $this->updateObjectModel(
            $carrier,
            CannotUpdateCarrierException::class,
            $errorCode
        );
    }

    /**
     * Returns a single shop ID when the constraint is a single shop, and the list of shops associated to the carrier
     * when the constraint is for all shops
     *
     * @param CarrierId $carrierId
     * @param ShopConstraint $shopConstraint
     *
     * @return ShopId[]
     */
    public function getShopIdsByConstraint(CarrierId $carrierId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            return $this->getAssociatedShopIdsFromGroup($carrierId, $shopConstraint->getShopGroupId());
        }

        if ($shopConstraint->forAllShops()) {
            return $this->getAssociatedShopIds($carrierId);
        }

        return [$shopConstraint->getShopId()];
    }

    /**
     * @param CarrierId $carrierId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIds(CarrierId $carrierId): array
    {
        $shops = parent::getObjectModelAssociatedShopIds($carrierId->getValue(), 'carrier');

        return array_map(static function (int $shopId) {
            return new ShopId((int) $shopId);
        }, $shops);
    }

    /**
     * @param CarrierId $carrierId
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIdsFromGroup(CarrierId $carrierId, ShopGroupId $shopGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ps.id_shop')
            ->from($this->prefix . 'carrier_shop', 'ps')
            ->innerJoin(
                'ps',
                $this->prefix . 'shop',
                's',
                's.id_shop = ps.id_shop'
            )
            ->where('ps.id_carrier = :carrierId')
            ->andWhere('s.id_shop_group = :shopGroupId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->setParameter('carrierId', $carrierId->getValue())
        ;

        return array_map(static function (array $shop) {
            return new ShopId((int) $shop['id_shop']);
        }, $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * Create a new version of the carrier, or return the carrier as is if it don't have order linked.
     *
     * @param CarrierId $carrierId
     *
     * @return Carrier
     */
    public function getEditableOrNewVersion(CarrierId $carrierId): Carrier
    {
        // If the carrier don't have orders linked, we can return it as is
        if ($this->getOrdersCount($carrierId) === 0) {
            return $this->get($carrierId);
        }

        // Otherwise, we need to create a new version of the carrier
        // Get the carrier to duplicate
        $carrier = $this->get($carrierId);
        /** @var Carrier $newCarrier */
        $newCarrier = $carrier->duplicateObject();
        $carrier->deleted = true;
        $this->partiallyUpdateObjectModel($carrier, ['deleted'], CannotUpdateCarrierException::class);

        // Copy all others information like ranges, shops associated, ...
        $newCarrier->copyCarrierData($carrierId->getValue());
        $this->update($newCarrier, CannotUpdateCarrierException::FAILED_UPDATE_CARRIER);
        $newCarrier->setGroups($carrier->getAssociatedGroupIds());

        // Return the new duplicated carrier
        return $newCarrier;
    }

    /**
     * @param CarrierId $carrierId
     * @param int[] $shopIds
     *
     * @return void
     */
    public function updateAssociatedShops(CarrierId $carrierId, array $shopIds): void
    {
        $this->updateObjectModelShopAssociations(
            $carrierId->getValue(),
            Carrier::class,
            $shopIds
        );
    }

    /**
     * Return all zones associated for the given carrier
     *
     * @param CarrierId $carrierId
     *
     * @return array
     */
    public function getAssociatedZones(CarrierId $carrierId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select('id_zone')
            ->from($this->prefix . 'carrier_zone')
            ->where('id_carrier = :carrierId')
            ->setParameter('carrierId', $carrierId->getValue())
            ->executeQuery()
            ->fetchFirstColumn();

        return $result;
    }

    /**
     * We add the association between the carrier and the zone.
     *
     * @param CarrierId $carrierId
     * @param int[] $zoneIds
     *
     * @return void
     */
    public function updateAssociatedZones(CarrierId $carrierId, array $zoneIds): void
    {
        $this->connection->beginTransaction();

        $this->connection->delete($this->prefix . 'carrier_zone', [
            'id_carrier' => $carrierId->getValue(),
        ]);

        foreach ($zoneIds as $zoneId) {
            $this->connection->insert(
                $this->prefix . 'carrier_zone',
                [
                    'id_carrier' => $carrierId->getValue(),
                    'id_zone' => $zoneId,
                ]
            );
        }

        $this->connection->commit();
    }

    public function getTaxRulesGroup(CarrierId $carrierId, ShopConstraint $shopConstraint): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ct.id_tax_rules_group')
            ->from($this->prefix . 'carrier_tax_rules_group_shop', 'ct')
            ->where('ct.id_carrier = :carrierId')
            ->setParameter('carrierId', $carrierId->getValue())
            // In case of multiple shops (for all shops and group shop) we fetch the first one
            // This is not strictly incorrect but until we decide how we handle multi shop we have no better solution
            ->orderBy('ct.id_shop')
            ->setMaxResults(1)
        ;

        if ($shopConstraint->getShopId()) {
            $qb
                ->andWhere('ct.id_shop = :shopId')
                ->setparameter('shopId', $shopConstraint->getShopId()->getValue())
            ;
        }
        $id = (int) $qb->fetchOne();

        return $id;
    }

    public function setTaxRulesGroup(CarrierId $carrierId, TaxRulesGroupId $taxRulesGroupId, ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->getShopId()) {
            $shopIdsToClean = $shopIdsToUpdate = [$shopConstraint->getShopId()->getValue()];
        } elseif ($shopConstraint->forAllShops()) {
            $shopIdsToUpdate = $this->getObjectModelAssociatedShopIds($carrierId->getValue(), Carrier::class);
            $shopIdsToClean = $this->shopRepository->getAssociatedShopIds($shopConstraint);
        } else {
            throw new RuntimeException('Cannot handle this shop constraint');
        }
        $this->deleteTaxRulesGroup($carrierId, $shopIdsToClean);

        // Doctrine doesn't handle bulk insert so e must insert each ro one by one
        foreach ($shopIdsToUpdate as $shopId) {
            $this->connection->insert(
                $this->prefix . 'carrier_tax_rules_group_shop',
                [
                    'id_carrier' => $carrierId->getValue(),
                    'id_tax_rules_group' => $taxRulesGroupId->getValue(),
                    'id_shop' => $shopId,
                ]
            );
        }
    }

    /**
     * @param CarrierId $carrierId
     * @param int[] $shopIds
     *
     * @return void
     */
    private function deleteTaxRulesGroup(CarrierId $carrierId, array $shopIds): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->delete($this->prefix . 'carrier_tax_rules_group_shop')
            ->where('id_carrier = :carrierId')
            ->andwhere('id_shop IN (:shopIds)')
            ->setParameter('carrierId', $carrierId->getValue())
            ->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER)
        ;

        $qb->executeStatement();
    }

    public function getOrdersCount(CarrierId $carrierId): int
    {
        $qb = $this->connection->createQueryBuilder();

        $count = $qb->select('COUNT(*)')
            ->from($this->prefix . 'order_carrier', 'oc')
            ->where('oc.id_carrier = :carrierId')
            ->setParameter('carrierId', $carrierId->getValue())
            ->executeQuery()
            ->fetchOne();

        return $count;
    }

    public function getLastPosition(): int
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb->select('c.position')
            ->from($this->prefix . 'carrier', 'c')
            ->orderBy('c.position', 'DESC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne()
        ;
    }
}
