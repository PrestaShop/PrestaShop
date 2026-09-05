<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Core\Security\AdminEmployeeContext;

/**
 * Collects contextual Admin Bar actions from Core providers and installed modules.
 *
 * Modules contribute through actionAdminBarGetActions and receive stable page identifiers in
 * the hook parameters. Only actions with an explicit Back Office endpoint are retained.
 */
final class AdminBarActionResolver
{
    private const MODULE_ACTIONS_HOOK = 'actionAdminBarGetActions';

    /**
     * @param iterable<AdminBarActionProviderInterface> $providers
     */
    public function __construct(
        private readonly iterable $providers,
        private readonly HookManager $hookManager,
    ) {
    }

    /**
     * @return list<AdminBarAction>
     */
    public function getActions(AdminBarPageContext $pageContext, AdminEmployeeContext $employeeContext): array
    {
        $actions = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->getActions($pageContext, $employeeContext) as $action) {
                $actions[] = $action;
            }
        }

        $moduleResults = $this->hookManager->exec(
            self::MODULE_ACTIONS_HOOK,
            [
                'pageContext' => $pageContext,
                'employeeContext' => $employeeContext,
                'ownerModule' => $pageContext->getOwnerModule(),
                'controller' => $pageContext->getControllerName(),
            ],
            null,
            true,
        );

        if (!is_array($moduleResults)) {
            return $actions;
        }

        foreach ($moduleResults as $moduleActions) {
            if (!is_iterable($moduleActions)) {
                continue;
            }

            foreach ($moduleActions as $action) {
                if ($action instanceof AdminBarAction && $action->getEndpoint() !== null) {
                    $actions[] = $action;
                }
            }
        }

        return $actions;
    }
}
