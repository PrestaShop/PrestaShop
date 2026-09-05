<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\AdminBar;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\AdminBar\AdminBarAction;
use PrestaShop\PrestaShop\Adapter\AdminBar\AdminBarActionResolver;
use PrestaShop\PrestaShop\Adapter\AdminBar\AdminBarPageContext;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Core\Security\AdminEmployeeContext;
use stdClass;

final class AdminBarActionResolverTest extends TestCase
{
    public function testItCollectsValidActionsReturnedByModules(): void
    {
        $hookManager = $this->createMock(HookManager::class);
        $hookManager->expects(self::once())
            ->method('exec')
            ->with(
                'actionAdminBarGetActions',
                self::callback(static fn (array $parameters): bool => isset($parameters['pageContext'], $parameters['employeeContext'])
                    && $parameters['ownerModule'] === 'testmodule'
                    && $parameters['controller'] === 'test-controller'),
                null,
                true,
            )
            ->willReturn([
                'testmodule' => [
                    new AdminBarAction('module_edit', 'Edit module', [], '/testmodule/12'),
                    'invalid action',
                    new AdminBarAction('missing_endpoint', 'Missing endpoint'),
                ],
            ]);

        $resolver = new AdminBarActionResolver([], $hookManager);
        $actions = $resolver->getActions(
            new AdminBarPageContext(new stdClass(), 'module-page', null, null, 'testmodule', 'test-controller'),
            new AdminEmployeeContext(12, 3),
        );

        self::assertCount(1, $actions);
        self::assertSame('module_edit', $actions[0]->getName());
    }
}
