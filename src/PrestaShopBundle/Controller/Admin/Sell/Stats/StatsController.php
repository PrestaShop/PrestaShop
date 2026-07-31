<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Stats;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\KpiRefreshProviderLocator;
use PrestaShop\PrestaShop\Adapter\Stats\StatsModuleGraphRenderer;
use PrestaShop\PrestaShop\Adapter\Stats\StatsModuleGridRenderer;
use PrestaShop\PrestaShop\Adapter\Stats\StatsModuleMenuProvider;
use PrestaShop\PrestaShop\Core\Employee\StatsDateRangeSetterInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\Exception\UnknownKpiException;
use PrestaShop\PrestaShop\Core\Stats\Exception\StatsModuleNotFoundException;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for the "Sell > Stats" page.
 */
class StatsController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        StatsModuleMenuProvider $statsModuleMenuProvider,
        StatsDateRangeSetterInterface $statsDateRangeSetter
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');
        $modules = $statsModuleMenuProvider->getModules();

        $currentModule = $request->query->get('module');
        if (!$currentModule && !empty($modules)) {
            $currentModule = 'statsforecast';
        }

        return $this->render('@PrestaShop/Admin/Sell/Stats/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'modules' => $modules,
            'currentModule' => $currentModule,
            'dateRange' => $statsDateRangeSetter->getDateRange(),
        ]);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function getKpiAction(Request $request, KpiRefreshProviderLocator $kpiRefreshProviderLocator): JsonResponse
    {
        $kpiKey = (string) $request->query->get('kpi');

        try {
            $provider = $kpiRefreshProviderLocator->getProvider($kpiKey);
            $kpiValue = $provider->getValue($request->query->all());
        } catch (UnknownKpiException) {
            return new JsonResponse(['has_errors' => true]);
        }

        return new JsonResponse([
            'value' => $kpiValue->getValue(),
            'tooltip' => $kpiValue->getTooltip(),
        ]);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function graphDrawAction(Request $request, StatsModuleGraphRenderer $statsModuleGraphRenderer): Response
    {
        try {
            $content = $statsModuleGraphRenderer->draw(
                (string) $request->query->get('module'),
                (string) $request->query->get('render'),
                (string) $request->query->get('type'),
                (int) $request->query->get('width'),
                (int) $request->query->get('height'),
                $request->query->get('layers'),
                $request->query->get('option'),
                (int) $request->query->get('id_employee'),
                (int) $request->query->get('id_lang')
            );
        } catch (StatsModuleNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return new Response($content);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function graphGridAction(Request $request, StatsModuleGridRenderer $statsModuleGridRenderer): Response
    {
        try {
            $content = $statsModuleGridRenderer->render(
                (string) $request->query->get('module'),
                (string) $request->query->get('render'),
                (string) $request->query->get('type'),
                (int) $request->query->get('width', 600),
                (int) $request->query->get('height', 920),
                (int) $request->query->get('start', 0),
                (int) $request->query->get('limit', 40),
                $request->query->get('sort', 0),
                $request->query->get('dir', 0),
                $request->query->get('option'),
                (int) $request->query->get('id_employee'),
                (int) $request->query->get('id_lang')
            );
        } catch (StatsModuleNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return new Response($content);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function setDateRangeAction(Request $request, StatsDateRangeSetterInterface $statsDateRangeSetter): JsonResponse
    {
        $dateFrom = (string) $request->request->get('datepickerFrom');
        $dateTo = (string) $request->request->get('datepickerTo');

        if ('' === $dateFrom || '' === $dateTo || strtotime($dateFrom) === false || strtotime($dateTo) === false || strtotime($dateFrom) > strtotime($dateTo)) {
            return new JsonResponse([
                'has_errors' => true,
                'errors' => [$this->trans('The specified date is invalid.', [], 'Admin.Stats.Notification')],
            ]);
        }

        $statsDateRangeSetter->setDateRange($dateFrom, $dateTo);

        return new JsonResponse([
            'has_errors' => false,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }
}
