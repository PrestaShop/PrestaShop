<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use PrestaShopBundle\Entity\AdminFilter;

/**
 * This repository is responsible of management of Administration filters.
 */
class AdminFilterRepository extends EntityRepository
{
    /**
     * @param int $employeeId
     * @param int $shopId
     * @param string $controller
     * @param string $action
     *
     * @return AdminFilter|null
     */
    public function findByEmployeeAndRouteParams($employeeId, $shopId, $controller, $action)
    {
        return $this->findOneBy([
            'employee' => $employeeId ?: 0,
            'shop' => $shopId ?: 0,
            'controller' => $controller,
            'action' => $action,
        ]);
    }

    /**
     * @param int $employeeId
     * @param int $shopId
     * @param string $filterId
     *
     * @return AdminFilter|null
     */
    public function findByEmployeeAndFilterId($employeeId, $shopId, $filterId)
    {
        return $this->findOneBy([
            'employee' => $employeeId ?: 0,
            'shop' => $shopId ?: 0,
            'filterId' => $filterId,
        ]);
    }

    /**
     * @param int $employeeId
     * @param int $shopId
     * @param string $controller
     * @param string $action
     *
     * @return bool Returns false if entity was not found
     *
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     */
    public function removeByEmployeeAndRouteParams($employeeId, $shopId, $controller, $action)
    {
        $adminFilter = $this->findByEmployeeAndRouteParams($employeeId, $shopId, $controller, $action);

        if (null === $adminFilter) {
            return false;
        }

        $this->getEntityManager()->remove($adminFilter);
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * Removes filters from ps_admin_filter `filters` column using provided AdminFilter entity.
     *
     * @param AdminFilter $adminFilter
     *
     * @throws OptimisticLockException
     */
    public function unsetFilters(AdminFilter $adminFilter)
    {
        $currentFilters = json_decode($adminFilter->getFilter(), true);

        // reset offset to show first page of list after filters resetting
        $currentFilters['offset'] = 0;
        // unset list columns filters
        unset($currentFilters['filters']);
        $adminFilter->setFilter(json_encode($currentFilters));

        $this->getEntityManager()->persist($adminFilter);
        $this->getEntityManager()->flush();
    }

    /**
     * Updates and persists modification to a filter (that was previously modified).
     *
     * @param AdminFilter $adminFilter
     */
    public function updateFilter(AdminFilter $adminFilter): void
    {
        $this->getEntityManager()->persist($adminFilter);
        $this->getEntityManager()->flush();
    }

    /**
     * Persist (create or update) filters into database using employee and uuid
     *
     * @param int $employeeId
     * @param int $shopId
     * @param array $filters
     * @param string $filterId
     *
     * @throws OptimisticLockException
     */
    public function createOrUpdateByEmployeeAndFilterId(
        $employeeId,
        $shopId,
        array $filters,
        $filterId
    ) {
        $adminFilter = $this->findByEmployeeAndFilterId($employeeId, $shopId, $filterId);
        $adminFilter = null === $adminFilter ? new AdminFilter() : $adminFilter;

        $adminFilter
            ->setController('')
            ->setAction('')
            ->setFilterId($filterId)
            ->setEmployee($employeeId)
            ->setShop($shopId)
            ->setFilter(json_encode($filters))
        ;

        $this->getEntityManager()->persist($adminFilter);
        $this->getEntityManager()->flush();
    }

    /**
     * Persist (create or update) filters into database using employee and controller name and its action name.
     *
     * @param int $employeeId
     * @param int $shopId
     * @param array $filters
     * @param string $controller
     * @param string $action
     *
     * @throws OptimisticLockException
     */
    public function createOrUpdateByEmployeeAndRouteParams(
        $employeeId,
        $shopId,
        $filters,
        $controller,
        $action
    ) {
        $adminFilter = $this->findOneBy([
            'employee' => $employeeId,
            'shop' => $shopId,
            'controller' => $controller,
            'action' => $action,
        ]);

        $adminFilter = null === $adminFilter ? new AdminFilter() : $adminFilter;

        $adminFilter
            ->setController($controller)
            ->setAction($action)
            ->setFilterId('')
            ->setEmployee($employeeId)
            ->setShop($shopId)
            ->setFilter(json_encode($filters))
        ;

        $this->getEntityManager()->persist($adminFilter);
        $this->getEntityManager()->flush();
    }
}
