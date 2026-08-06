<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\AdminGridView;

/**
 * @extends EntityRepository<AdminGridView>
 */
class AdminGridViewRepository extends EntityRepository
{
    /**
     * @return AdminGridView[]
     */
    public function findSharedViews(int $shopId, string $gridId, string $controllerRoute, int $excludedEmployeeId): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.gridConfiguration', 'c')
            ->andWhere('f.shared = true')
            ->andWhere('c.shopId = :shopId')
            ->andWhere('c.gridId = :gridId')
            ->andWhere('c.controllerRoute = :controllerRoute')
            ->andWhere('c.employeeId != :employeeId')
            ->setParameter('shopId', $shopId)
            ->setParameter('gridId', $gridId)
            ->setParameter('controllerRoute', $controllerRoute)
            ->setParameter('employeeId', $excludedEmployeeId)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
