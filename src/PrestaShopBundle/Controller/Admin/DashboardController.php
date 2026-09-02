<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Form\Admin\Dashboard\DashboardDateRangeType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for the Symfony (migrated) "Dashboard" page.
 *
 * Unlike the legacy page, this controller makes no assumption about module internals:
 * it neither registers Smarty template directories nor shims the legacy controller for
 * `get_class()` checks. Instead a new, dedicated hook family is dispatched (from the Twig
 * layout, via `renderhook`) so a module knows which architecture it integrates with
 * purely from which hook is called.
 */
class DashboardController extends PrestaShopAdminController
{
    /**
     * Dashboard hooks that receive the date range as parameters. The date range selector
     * is only displayed when at least one module is registered on one of them.
     */
    private const DATE_RANGE_HOOKS = [
        'displayAdminDashboardZoneOne',
        'displayAdminDashboardZoneTwo',
        'displayAdminDashboardZoneThree',
        'displayAdminDashboardTop',
        'displayAdminDashboardBottom',
    ];

    /**
     * Display the migrated dashboard: resolve the per-employee date range and render the
     * Twig layout, which dispatches the dashboard hooks.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        HookInformationProvider $hookInformationProvider,
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        $employeeId = $this->getEmployeeContext()->getEmployee()?->getId();
        $employee = null !== $employeeId
            ? $entityManager->getRepository(Employee::class)->find($employeeId)
            : null;

        // Effective range: the employee's stored stats range, or the same default as the
        // legacy page (last month → today) when none has been set yet.
        $dateFrom = $employee?->getStatsDateFrom() ?? new DateTime('-1 month');
        $dateTo = $employee?->getStatsDateTo() ?? new DateTime();

        // Only offer the date range selector when a module actually consumes the range,
        // i.e. is registered on one of the date-aware hooks. A dashboard made only of, say,
        // a toolbar banner should not display a useless date picker.
        $dateRangeFormView = null;
        if ($this->isDateRangeUsed($hookInformationProvider)) {
            $dateRangeForm = $this->createForm(DashboardDateRangeType::class, [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]);
            $dateRangeForm->handleRequest($request);

            // Persist a submitted range on the employee so the page reflects it (skipped in
            // demo mode; Doctrine's flush is a no-op when nothing actually changed).
            if (
                $dateRangeForm->isSubmitted()
                && $dateRangeForm->isValid()
                && null !== $employee
                && !$this->isDemoModeEnabled()
            ) {
                $data = $dateRangeForm->getData();
                $dateFrom = $data['date_from'];
                $dateTo = $data['date_to'];
                $employee->setStatsDateFrom($dateFrom);
                $employee->setStatsDateTo($dateTo);
                $entityManager->flush();
            }

            $dateRangeFormView = $dateRangeForm->createView();
        }

        return $this->render('@PrestaShop/Admin/Dashboard/index.html.twig', [
            'layoutTitle' => $this->trans('Dashboard', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'dateRangeForm' => $dateRangeFormView,
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
        ]);
    }

    /**
     * Whether at least one module is registered on a hook that consumes the date range.
     */
    private function isDateRangeUsed(HookInformationProvider $hookInformationProvider): bool
    {
        foreach (self::DATE_RANGE_HOOKS as $hookName) {
            if (!empty($hookInformationProvider->getRegisteredModulesByHookName($hookName))) {
                return true;
            }
        }

        return false;
    }
}
