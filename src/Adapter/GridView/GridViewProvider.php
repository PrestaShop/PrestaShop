<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\GridView;

use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewAccessDeniedException;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewException;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;

/**
 * Provides AdminGridView entities while enforcing the access rules of the current employee.
 */
class GridViewProvider
{
    /**
     * @param AdminGridViewRepository $gridViewRepository
     * @param EmployeeContext $employeeContext
     */
    public function __construct(
        private readonly AdminGridViewRepository $gridViewRepository,
        private readonly EmployeeContext $employeeContext,
    ) {
    }

    /**
     * A view can only be modified by the employee owning it, on a shop they are authorized on.
     *
     * @param GridViewId $gridViewId
     *
     * @return AdminGridView
     *
     * @throws GridViewNotFoundException
     * @throws GridViewAccessDeniedException
     */
    public function getOwnedGridView(GridViewId $gridViewId): AdminGridView
    {
        $gridView = $this->getGridView($gridViewId);
        $configuration = $gridView->getGridConfiguration();
        $employee = $this->employeeContext->getEmployee();

        if (null === $employee
            || $configuration->getEmployeeId() !== $employee->getId()
            || !$this->employeeContext->hasAuthorizationOnShop($configuration->getShopId())
        ) {
            throw new GridViewAccessDeniedException(sprintf('Grid view %d cannot be modified by the current employee', $gridViewId->getValue()));
        }

        return $gridView;
    }

    /**
     * A view can be used by its owner or, when shared, by any employee authorized on its shop.
     *
     * @param GridViewId $gridViewId
     *
     * @return AdminGridView
     *
     * @throws GridViewNotFoundException
     * @throws GridViewAccessDeniedException
     */
    public function getAccessibleGridView(GridViewId $gridViewId): AdminGridView
    {
        $gridView = $this->getGridView($gridViewId);
        $configuration = $gridView->getGridConfiguration();
        $employee = $this->employeeContext->getEmployee();

        $isOwn = null !== $employee && $configuration->getEmployeeId() === $employee->getId();
        if ((!$isOwn && !$gridView->isShared())
            || !$this->employeeContext->hasAuthorizationOnShop($configuration->getShopId())
        ) {
            throw new GridViewAccessDeniedException(sprintf('Grid view %d cannot be used by the current employee', $gridViewId->getValue()));
        }

        return $gridView;
    }

    /**
     * @return int
     *
     * @throws GridViewException
     */
    public function getCurrentEmployeeId(): int
    {
        $employee = $this->employeeContext->getEmployee();

        if (null === $employee) {
            throw new GridViewException('No employee in context', GridViewException::MISSING_EMPLOYEE);
        }

        return $employee->getId();
    }

    /**
     * @param GridViewId $gridViewId
     *
     * @return AdminGridView
     *
     * @throws GridViewNotFoundException
     */
    private function getGridView(GridViewId $gridViewId): AdminGridView
    {
        $gridView = $this->gridViewRepository->find($gridViewId->getValue());

        if (null === $gridView) {
            throw new GridViewNotFoundException(sprintf('Grid view %d was not found', $gridViewId->getValue()));
        }

        return $gridView;
    }
}
