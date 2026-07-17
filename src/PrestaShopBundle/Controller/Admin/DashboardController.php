<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for the Symfony (migrated) "Dashboard" page.
 *
 * This page is gated behind the `dashboard` feature flag (beta). While the flag is
 * disabled the legacy AdminDashboardController keeps serving the page unchanged.
 *
 * Unlike the legacy page, this controller makes no assumption about module internals:
 * it neither registers Smarty template directories nor shims the legacy controller for
 * `get_class()` checks. Instead it dispatches a new, dedicated hook family so a module
 * knows which architecture it integrates with purely from which hook is called.
 */
class DashboardController extends PrestaShopAdminController
{
    /**
     * Display the migrated dashboard: resolve the per-employee date range, dispatch the
     * new dashboard zone hooks and render the Twig layout.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire(service: HookDispatcherInterface::class)]
        HookDispatcherInterface $hookDispatcher,
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        $employeeId = $this->getEmployeeContext()->getEmployee()?->getId();
        $employee = null !== $employeeId
            ? $entityManager->getRepository(Employee::class)->find($employeeId)
            : null;

        // Reuse the existing per-employee stats date configuration. When a valid range is
        // submitted (GET form), persist it on the employee so the page reflects it. The
        // write is skipped in demo mode (Doctrine's flush is a no-op when nothing changed).
        $submittedFrom = $this->toDate($request->query->get('date_from'));
        $submittedTo = $this->toDate($request->query->get('date_to'));
        if (
            null !== $employee
            && null !== $submittedFrom
            && null !== $submittedTo
            && !$this->isDemoModeEnabled()
        ) {
            $employee->setStatsDateFrom($submittedFrom);
            $employee->setStatsDateTo($submittedTo);
            $entityManager->flush();
        }

        // Fall back to the same default range as the legacy page (last month → today)
        // when the employee has no stored range yet.
        $dateFrom = ($employee?->getStatsDateFrom() ?? new DateTime('-1 month'))->format('Y-m-d');
        $dateTo = ($employee?->getStatsDateTo() ?? new DateTime())->format('Y-m-d');

        $hookParameters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];

        return $this->render('@PrestaShop/Admin/Dashboard/index.html.twig', [
            'layoutTitle' => $this->trans('Dashboard', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'zoneOneContent' => $this->renderDashboardHook($hookDispatcher, 'displayAdminDashboardZoneOne', $hookParameters),
            'zoneTwoContent' => $this->renderDashboardHook($hookDispatcher, 'displayAdminDashboardZoneTwo', $hookParameters),
            'zoneThreeContent' => $this->renderDashboardHook($hookDispatcher, 'displayAdminDashboardZoneThree', $hookParameters),
        ]);
    }

    /**
     * Dispatch a rendering hook and return the concatenated HTML output of every listener.
     */
    private function renderDashboardHook(HookDispatcherInterface $hookDispatcher, string $hookName, array $parameters): string
    {
        return $hookDispatcher
            ->dispatchRenderingWithParameters($hookName, $parameters)
            ->outputContent();
    }

    /**
     * Parse a strict `Y-m-d` string into a DateTime (time zeroed), or return null when invalid.
     */
    private function toDate(mixed $value): ?DateTime
    {
        if (!is_string($value) || '' === $value) {
            return null;
        }

        $date = DateTime::createFromFormat('!Y-m-d', $value);

        return $date && $date->format('Y-m-d') === $value ? $date : null;
    }
}
