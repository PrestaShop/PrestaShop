<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Repository\AdminGridConfigurationRepository;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;

class GridViewsPresenter
{
    /**
     * @param AdminGridConfigurationRepository $configurationRepository
     * @param AdminGridViewRepository $gridViewRepository
     * @param GridViewUrlGenerator $urlGenerator
     * @param EmployeeContext $employeeContext
     * @param ShopContext $shopContext
     */
    public function __construct(
        private readonly AdminGridConfigurationRepository $configurationRepository,
        private readonly AdminGridViewRepository $gridViewRepository,
        private readonly GridViewUrlGenerator $urlGenerator,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopContext $shopContext,
    ) {
    }

    /**
     * @return array<int, array{id: int, name: string, url: string, is_own: bool, is_shared: bool}>
     */
    public function presentViews(string $gridId, string $controllerRoute): array
    {
        $employee = $this->employeeContext->getEmployee();
        if (null === $employee) {
            return [];
        }

        $presentedViews = [];
        foreach ($this->findVisibleViews($gridId, $controllerRoute) as $gridView) {
            $presentedView = $this->presentView($gridView, $employee->getId());
            if (null !== $presentedView) {
                $presentedViews[] = $presentedView;
            }
        }

        return $presentedViews;
    }

    /**
     * @return AdminGridView[]
     */
    public function findVisibleViews(string $gridId, string $controllerRoute): array
    {
        $employee = $this->employeeContext->getEmployee();
        if (null === $employee) {
            return [];
        }

        $shopId = $this->shopContext->getId();
        $configuration = $this->configurationRepository->findForEmployee($employee->getId(), $shopId, $gridId, $controllerRoute);

        $views = [];
        if (null !== $configuration) {
            $views = $configuration->getViews()->toArray();
        }

        if (null === $configuration || $configuration->displaySharedFilters()) {
            $views = array_merge(
                $views,
                $this->gridViewRepository->findSharedViews($shopId, $gridId, $controllerRoute, $employee->getId())
            );
        }

        return $views;
    }

    /**
     * @return array{id: int, name: string, url: string, is_own: bool, is_shared: bool}|null
     */
    private function presentView(AdminGridView $gridView, int $employeeId): ?array
    {
        try {
            $url = $this->urlGenerator->generate($gridView);
        } catch (RoutingException) {
            return null;
        }

        return [
            'id' => $gridView->getId(),
            'name' => $gridView->getName(),
            'url' => $url,
            'is_own' => $gridView->getGridConfiguration()->getEmployeeId() === $employeeId,
            'is_shared' => $gridView->isShared(),
        ];
    }
}
