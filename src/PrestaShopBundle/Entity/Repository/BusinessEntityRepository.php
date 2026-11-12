<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityCustomerB2b;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class BusinessEntityRepository extends EntityRepository
{
    public function save(BusinessEntity $businessEntity): int
    {
        $this->getEntityManager()->persist($businessEntity);
        $this->getEntityManager()->flush();

        return $businessEntity->getId();
    }

    /**
     * @param int[]|null $shopIds
     *
     * @throws NonUniqueResultException
     */
    public function findById(int $businessEntityId, ?array $shopIds = null): ?BusinessEntity
    {
        /** @var BusinessEntity|null $businessEntity */
        $businessEntity = $this->createByShopIdsQueryBuilder($shopIds)
            ->leftJoin('be.businessEntityIdentifiers', 'bei')
            ->addSelect('bei')
            ->leftJoin('bei.businessIdentifier', 'bi')
            ->addSelect('bi')
            ->andWhere('be.id = :businessEntityId')
            ->setParameter('businessEntityId', $businessEntityId)
            ->getQuery()
            ->getOneOrNullResult();

        return $businessEntity;
    }

    public function delete(BusinessEntity $businessEntity): void
    {
        $businessEntity->setDeleted(true);
        $this->getEntityManager()->flush();
    }

    /**
     * Counts every b2b customer linked to the entity, whatever the customer's own b2b status:
     * a link is a link, and filtering on the customer status is deliberately left out until
     * the link management screens exist.
     */
    public function getLinkedCustomersCount(int $businessEntityId): int
    {
        return (int) $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(bec.id)')
            ->from(BusinessEntityCustomerB2b::class, 'bec')
            ->where('bec.businessEntity = :businessEntityId')
            ->setParameter('businessEntityId', $businessEntityId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param int[]|null $shopIds
     */
    public function getPendingCount(?array $shopIds = null): int
    {
        return (int) $this->createByShopIdsQueryBuilder($shopIds)
            ->select('COUNT(be.id)')
            ->andWhere('be.status = :status')
            ->setParameter('status', BusinessEntityStatus::PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int[]|null $shopIds
     */
    public function createByShopIdsQueryBuilder(?array $shopIds): QueryBuilder
    {
        $qb = $this
            ->createQueryBuilder('be')
            ->where('be.deleted = false');

        if (null === $shopIds) {
            return $qb;
        }

        return $qb
            ->andWhere('be.idShop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds);
    }
}
