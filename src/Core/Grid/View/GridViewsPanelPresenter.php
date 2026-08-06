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
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShopBundle\Entity\Repository\AdminGridConfigurationRepository;
use PrestaShopBundle\Form\Admin\Grid\GridConfigurationType;
use PrestaShopBundle\Form\Admin\Grid\GridViewType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class GridViewsPanelPresenter
{
    public const SAVE_FORM_NAME_PREFIX = 'grid_view_';
    public const CONFIGURATION_FORM_NAME_PREFIX = 'grid_configuration_';

    /**
     * @param FeatureFlagStateCheckerInterface $featureFlagStateChecker
     * @param EmployeeContext $employeeContext
     * @param ShopContext $shopContext
     * @param AdminGridConfigurationRepository $configurationRepository
     * @param FormFactoryInterface $formFactory
     * @param RequestStack $requestStack
     */
    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopContext $shopContext,
        private readonly AdminGridConfigurationRepository $configurationRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @param GridInterface $grid
     *
     * @return array|null
     */
    public function present(GridInterface $grid): ?array
    {
        if (!$this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_GRID_VIEWS)
            || !$this->shopContext->isSingleShopContext()
        ) {
            return null;
        }

        $employee = $this->employeeContext->getEmployee();
        $request = $this->requestStack->getCurrentRequest();
        $route = $request?->attributes->get('_route');

        if (null === $employee || empty($route) || !is_string($route)) {
            return null;
        }

        $gridId = $grid->getDefinition()->getId();
        $gridState = GridState::fromGrid($grid);
        $configuration = $this->configurationRepository->findForEmployee(
            $employee->getId(),
            $this->shopContext->getId(),
            $gridId,
            $route
        );

        $displaySharedFilters = $configuration?->displaySharedFilters() ?? true;
        $displayTotals = $configuration?->displayTotals() ?? true;

        $saveViewForm = $this->formFactory->createNamed(
            self::SAVE_FORM_NAME_PREFIX . $gridId,
            GridViewType::class,
            [
                'controller_route' => $route,
                'filter_id' => $gridState->filterId,
                'grid_state' => json_encode($gridState->toArray()),
            ],
            [
                'active_date_filters' => $gridState->getActiveDateRangeFilters(),
            ]
        );

        $configurationForm = $this->formFactory->createNamed(
            self::CONFIGURATION_FORM_NAME_PREFIX . $gridId,
            GridConfigurationType::class,
            [
                'display_shared_filters' => $displaySharedFilters,
                'display_totals' => $displayTotals,
                'controller_route' => $route,
                'filter_id' => $gridState->filterId,
            ]
        );

        return [
            'grid_id' => $gridId,
            'filter_id' => $gridState->filterId,
            'controller_route' => $route,
            'display_totals' => $displayTotals,
            'selected_view_id' => $request->query->getInt(GridViewUrlGenerator::SELECTED_VIEW_PARAM),
            'save_view_form' => $saveViewForm->createView(),
            'configuration_form' => $configurationForm->createView(),
        ];
    }
}
