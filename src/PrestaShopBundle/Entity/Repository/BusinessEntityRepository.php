<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
        $qb = $this->createQueryBuilder('be')
            ->leftJoin('be.businessEntityIdentifiers', 'bei')
            ->addSelect('bei')
            ->leftJoin('bei.businessIdentifier', 'bi')
            ->addSelect('bi')
            ->where('be.id = :businessEntityId')
            ->andWhere('be.deleted = false')
            ->setParameter('businessEntityId', $businessEntityId);

        if (null !== $shopIds) {
            $qb->andWhere('be.idShop IN (:shopIds)')
                ->setParameter('shopIds', $shopIds);
        }

        /** @var BusinessEntity|null $businessEntity */
        $businessEntity = $qb->getQuery()->getOneOrNullResult();

        return $businessEntity;
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
        $qb = $this->createQueryBuilder('be')
            ->select('COUNT(be.id)')
            ->where('be.status = :status')
            ->andWhere('be.deleted = false')
            ->setParameter('status', BusinessEntityStatus::PENDING);

        if (null !== $shopIds) {
            $qb->andWhere('be.idShop IN (:shopIds)')
                ->setParameter('shopIds', $shopIds);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
