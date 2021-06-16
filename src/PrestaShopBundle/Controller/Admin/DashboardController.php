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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\UpdateEmployeeStatsRangesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\CannotUpdateEmployeeStatsRanges;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends FrameworkBundleAdminController
{
    /**
     * Renders dashboard page.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $context = $this->getContext();
        $isoCode = $context->language->iso_code;
        $helper = $this->get('prestashop.adapter.dashboard.helper');
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $hookDispatcher = $this->get('prestashop.core.hook.dispatcher');

        $params = [
            'date_from' => $context->employee->stats_date_from,
            'date_to' => $context->employee->stats_date_to,
        ];

        return $this->render('@PrestaShop/Admin/Dashboard/index.html.twig', [
            'newVersionUrl' => $helper->getNewVersionUrl($request),
            'langIso' => $isoCode,
            'helpCenterLink' => $helper->getDocumentationUrl($isoCode),
            'hostMode' => $helper->isHostMode(),
            'demoMode' => $configuration->getInt('PS_DASHBOARD_SIMULATION'),
            'hookDashboardZoneOne' => $hookDispatcher->dispatchRenderingWithParameters('dashboardZoneOne', $params)->outputContent(),
            'hookDashboardZoneTwo' => $hookDispatcher->dispatchRenderingWithParameters('dashboardZoneTwo', $params)->outputContent(),
            'preselectDateRange' => $request->request->get('preselectDateRange', $context->employee->preselect_date_range),
            'dateFrom' => $context->employee->stats_date_from,
            'dateTo' => $context->employee->stats_date_to,
            'dashboardUsePush' => $configuration->get('PS_DASHBOARD_USE_PUSH'),
            'd3JsPath' => $helper->getD3JavascriptPath(),
        ]);
    }

    /**
     * Returns JSON of latest blog posts.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @return JsonResponse
     */
    public function getBlogRssAction(): JsonResponse
    {
        $fetcher = $this->get('prestashop.adapter.news.provider');

        return $this->json($fetcher->getData($this->getContext()->language->iso_code));
    }

    /**
     * Sets demo mode of dashboard.
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function setSimulationModeAction(Request $request): Response
    {
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_DASHBOARD_SIMULATION', $request->query->getInt('simulation'));
        return Response::create('k' . $configuration->get('PS_DASHBOARD_SIMULATION') . 'k');
    }

    /**
     * Sets dashboard date range for dashboard.
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function submitDateRangeAction(Request $request): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new UpdateEmployeeStatsRangesCommand(
                (int) $this->getContext()->employee->id,
                (string) $request->request->get('date_from'),
                (string) $request->request->get('date_to'),
                $request->request->has('compare') && $request->request->getInt('compare'),
                (string) $request->request->get('compare_from'),
                (string) $request->request->get('compare_to')
            ));
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_dashboard_index');
    }

    /**
     * Returns dashboard data.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function refreshAction(Request $request): JsonResponse
    {
        $helper = $this->get('prestashop.adapter.dashboard.helper');
        $data = $helper->getDashboardData(
            $request->query->get('module'),
            $request->query->getBoolean('dashboard_use_push'),
            $request->query->getInt('extra')
        );
        return JsonResponse::create($data);
    }

    /**
     * Handles module dashboard configuration data save.
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveModuleOptionsAction(Request $request): JsonResponse
    {
        $helper = $this->get('prestashop.adapter.dashboard.helper');

        return JsonResponse::create($helper->handleModuleConfigurationSave($request));
    }

    /**
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            CannotUpdateEmployeeStatsRanges::class => [
                CannotUpdateEmployeeStatsRanges::INVALID_DATE => $this->trans(
                    'The selected date range is not valid.', 'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
