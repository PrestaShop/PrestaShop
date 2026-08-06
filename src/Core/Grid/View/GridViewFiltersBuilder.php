<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;
use Symfony\Component\HttpFoundation\Request;

class GridViewFiltersBuilder extends AbstractFiltersBuilder
{
    private ?Request $request = null;

    /**
     * @param AdminGridViewRepository $gridViewRepository
     * @param DynamicDateRuleApplier $dateRuleApplier
     * @param EmployeeContext $employeeContext
     * @param ShopContext $shopContext
     * @param FeatureFlagStateCheckerInterface $featureFlagStateChecker
     */
    public function __construct(
        private readonly AdminGridViewRepository $gridViewRepository,
        private readonly DynamicDateRuleApplier $dateRuleApplier,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopContext $shopContext,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    /**
     * @param array $config
     *
     * @return mixed
     */
    public function setConfig(array $config)
    {
        $this->request = isset($config['request']) && $config['request'] instanceof Request
            ? $config['request']
            : null;

        return parent::setConfig($config);
    }

    /**
     * @param Filters|null $filters
     *
     * @return mixed
     */
    public function buildFilters(?Filters $filters = null)
    {
        if (null === $filters || null === $this->request) {
            return $filters;
        }

        $gridViewId = $this->request->query->getInt(GridViewUrlGenerator::SELECTED_VIEW_PARAM);
        if ($gridViewId <= 0
            || !$this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_GRID_VIEWS)
            || !$this->shopContext->isSingleShopContext()
        ) {
            return $filters;
        }

        $gridView = $this->gridViewRepository->find($gridViewId);
        if (null === $gridView) {
            return $filters;
        }

        $configuration = $gridView->getGridConfiguration();

        if ($gridView->getFilterId() !== $this->getFilterId($filters)
            || $configuration->getControllerRoute() !== $this->request->attributes->get('_route')
        ) {
            return $filters;
        }

        if (!$this->canUseView($gridView->isShared(), $configuration->getEmployeeId(), $configuration->getShopId())) {
            return $filters;
        }

        $viewCriteria = json_decode($gridView->getFilters(), true) ?: [];
        $viewCriteria = $this->dateRuleApplier->apply($viewCriteria, $gridView->getDynamicDateRules() ?? []);

        $filters->set('filters', is_array($viewCriteria['filters'] ?? null) ? $viewCriteria['filters'] : []);
        $filters->set('offset', 0);
        foreach (['limit', 'orderBy', 'sortOrder'] as $criteriaKey) {
            if (isset($viewCriteria[$criteriaKey])) {
                $filters->set($criteriaKey, $viewCriteria[$criteriaKey]);
            }
        }

        return $filters;
    }

    /**
     * @param bool $isShared
     * @param int $ownerEmployeeId
     * @param int $shopId
     *
     * @return bool
     */
    private function canUseView(bool $isShared, int $ownerEmployeeId, int $shopId): bool
    {
        $employee = $this->employeeContext->getEmployee();

        if (null !== $employee && $ownerEmployeeId === $employee->getId()) {
            return true;
        }

        return $isShared && $shopId === $this->shopContext->getId();
    }
}
