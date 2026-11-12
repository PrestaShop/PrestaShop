<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Controllers;

use AdminDashboardControllerCore;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AdminDashboardControllerCoreTest extends KernelTestCase
{
    /**
     * The dashboard renders one widget zone per `dashboardZone*` hook it dispatches, and
     * js/admin/dashboard.js sends the id of the enclosing zone container back when a widget
     * saves its configuration. A zone missing from the allowed list therefore renders its
     * widget but rejects the save, so the two lists have to agree.
     */
    public function testEveryDispatchedZoneHookMayAlsoSaveItsConfiguration(): void
    {
        $dispatched = $this->dispatchedZoneHooks();

        // Guards against a regex that quietly matches nothing: the page has three zones.
        self::assertGreaterThanOrEqual(
            3,
            count($dispatched),
            'Failed to read the dispatched zone hooks out of the controller.'
        );

        $allowed = (new ReflectionClass(AdminDashboardControllerCore::class))
            ->getConstant('DASHBOARD_ALLOWED_HOOKS');
        self::assertIsArray($allowed);

        foreach ($dispatched as $hook) {
            self::assertContains(
                $hook,
                $allowed,
                sprintf('Zone hook "%s" is rendered by the dashboard but cannot save its configuration.', $hook)
            );
        }
    }

    /**
     * Replays what the browser sends: the zone container id, which is the hook name prefixed
     * with "hook", against the check the controller applies to it.
     */
    public function testTheConfigurationGuardAcceptsWhatTheZoneContainersSend(): void
    {
        $allowed = (new ReflectionClass(AdminDashboardControllerCore::class))
            ->getConstant('DASHBOARD_ALLOWED_HOOKS');

        foreach ($this->dispatchedZoneHooks() as $hook) {
            $containerId = 'hook' . ucfirst($hook);
            self::assertContains(
                lcfirst(str_replace('hook', '', $containerId)),
                $allowed,
                sprintf('The dashboard rejects a configuration save coming from container "%s".', $containerId)
            );
        }
    }

    /**
     * @return string[]
     */
    private function dispatchedZoneHooks(): array
    {
        $source = file_get_contents(_PS_ROOT_DIR_ . '/controllers/admin/AdminDashboardController.php');
        self::assertIsString($source);

        preg_match_all("/Hook::exec\('(dashboardZone[A-Za-z]+)'/", $source, $matches);

        return array_values(array_unique($matches[1]));
    }
}
