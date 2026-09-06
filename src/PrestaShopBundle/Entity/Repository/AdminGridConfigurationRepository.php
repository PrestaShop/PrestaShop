<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\AdminGridConfiguration;
use RuntimeException;

/**
 * @extends EntityRepository<AdminGridConfiguration>
 */
class AdminGridConfigurationRepository extends EntityRepository
{
    /**
     * @param AdminGridConfiguration $configuration
     *
     * @return void
     */
    public function save(AdminGridConfiguration $configuration): void
    {
        $this->getEntityManager()->persist($configuration);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $employeeId
     * @param int $shopId
     * @param string $gridId
     * @param string $controllerRoute
     *
     * @return AdminGridConfiguration|null
     */
    public function findForEmployee(int $employeeId, int $shopId, string $gridId, string $controllerRoute): ?AdminGridConfiguration
    {
        return $this->findOneBy([
            'employeeId' => $employeeId,
            'shopId' => $shopId,
            'gridId' => $gridId,
            'controllerRoute' => $controllerRoute,
        ]);
    }

    /**
     * @param int $employeeId
     * @param int $shopId
     * @param string $gridId
     * @param string $filterId
     * @param string $controllerRoute
     *
     * @return AdminGridConfiguration
     */
    public function findOrCreateForEmployee(
        int $employeeId,
        int $shopId,
        string $gridId,
        string $filterId,
        string $controllerRoute,
    ): AdminGridConfiguration {
        $configuration = $this->findForEmployee($employeeId, $shopId, $gridId, $controllerRoute);

        if (null !== $configuration) {
            return $configuration;
        }

        try {
            $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');
            $this->getEntityManager()->getConnection()->insert($this->getClassMetadata()->getTableName(), [
                'id_employee' => $employeeId,
                'id_shop' => $shopId,
                'grid_id' => $gridId,
                'filter_id' => $filterId,
                'controller_route' => $controllerRoute,
                'display_shared_filters' => 1,
                'display_totals' => 1,
                'date_add' => $now,
                'date_upd' => $now,
            ]);
        } catch (UniqueConstraintViolationException) {
            // Concurrent creation: the row now exists, load it below
        }

        $configuration = $this->findForEmployee($employeeId, $shopId, $gridId, $controllerRoute);

        if (null === $configuration) {
            throw new RuntimeException(sprintf('Unable to create the grid configuration of grid "%s"', $gridId));
        }

        return $configuration;
    }
}
