<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Security\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\EventListener\Admin\RequiredTwoFactorSetupListener;
use PrestaShopBundle\Security\Admin\AdminAuthenticationSuccessHandler;
use PrestaShopBundle\Security\Admin\EmployeeHomepageProvider;
use PrestaShopBundle\Security\Admin\TwoFactorSetupRequirementChecker;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RequiredTwoFactorSetupTest extends TestCase
{
    protected function setUp(): void
    {
        if (!interface_exists(TwoFactorInterface::class)) {
            self::markTestSkipped('The Employee entity requires the locked Scheb dependencies.');
        }
    }

    #[DataProvider('globalSettings')]
    public function testSuccessHandlerHonoursGlobalSetting(bool $global): void
    {
        [$employee, $repository, $checker] = $this->createDependencies($global);
        $homepage = $this->createMock(EmployeeHomepageProvider::class);
        $homepage->expects($global ? self::never() : self::once())->method('getHomepageUrl')->willReturn('/dashboard');
        $router = $this->createMock(RouterInterface::class);
        $router->expects($global ? self::once() : self::never())->method('generate')
            ->with('admin_employees_edit', ['employeeId' => 42])->willReturn('/employee/42');
        $handler = new AdminAuthenticationSuccessHandler($homepage, $repository, $router, $checker);

        $response = $handler->onAuthenticationSuccess(Request::create('/login'), new UsernamePasswordToken($employee, 'main', ['ROLE_EMPLOYEE']));

        self::assertSame($global ? '/employee/42' : '/dashboard', $response->headers->get('Location'));
    }

    #[DataProvider('requestStates')]
    public function testSetupListenerHonoursGlobalSettingAndOnlyAllowsEditingOwnProfile(
        bool $global,
        string $route,
        int $employeeId,
        bool $redirect,
        bool $mainRequest = true
    ): void {
        [$employee, $repository, $checker] = $this->createDependencies($global);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($employee);
        $router = $this->createMock(RouterInterface::class);
        $router->expects($redirect ? self::once() : self::never())->method('generate')
            ->with('admin_employees_edit', ['employeeId' => 42])->willReturn('/employee/42');
        $listener = new RequiredTwoFactorSetupListener($security, $repository, $router, $checker);
        $request = Request::create('/protected');
        $request->attributes->add(['_route' => $route, 'employeeId' => $employeeId]);
        $request->setSession(new Session(new MockArraySessionStorage()));
        $event = new RequestEvent($this->createMock(HttpKernelInterface::class), $request, $mainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST);

        $listener->onKernelRequest($event);

        self::assertSame($redirect, $event->hasResponse());
        self::assertSame($redirect, $event->isPropagationStopped());
        self::assertSame($redirect ? $request->getUri() : null, $request->getSession()->get('_security.main.target_path'));
    }

    public static function globalSettings(): iterable
    {
        yield 'feature disabled' => [false];
        yield 'feature enabled' => [true];
    }

    public static function requestStates(): iterable
    {
        yield 'disabled globally' => [false, 'admin_products_index', 0, false];
        yield 'required setup' => [true, 'admin_products_index', 0, true];
        yield 'own profile' => [true, 'admin_employees_edit', 42, false];
        yield 'another employee profile' => [true, 'admin_employees_edit', 43, true];
        yield 'logout' => [true, 'admin_logout', 0, false];
        yield 'subrequest' => [true, 'admin_products_index', 0, false, false];
    }

    /**
     * @return array{Employee, EmployeeRepository, TwoFactorSetupRequirementChecker}
     */
    private function createDependencies(bool $global): array
    {
        $employee = $this->createMock(Employee::class);
        $employee->method('getId')->willReturn(42);
        $employee->method('getUserIdentifier')->willReturn('employee@example.com');
        $employee->method('isTwoFactorRequired')->willReturn(true);
        $employee->method('getTwoFactorEnabled')->willReturn(false);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('loadEmployeeByIdentifier')->with('employee@example.com', true)->willReturn($employee);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('PS_BACKOFFICE_2FA')->willReturn($global);

        return [$employee, $repository, new TwoFactorSetupRequirementChecker($configuration)];
    }
}
