<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use Context;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\EmployeeSession;
use PrestaShopBundle\Entity\Employee\Profile;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\EventListener\Admin\EmployeeSessionSubscriber;
use PrestaShopBundle\Security\Admin\EmployeeProvider;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use Psr\Log\NullLogger;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Resources\classes\TestCookie;

final class EmployeeSessionSubscriberTest extends TestCase
{
    /**
     * @dataProvider getAdminRequests
     */
    public function testAdminPathIsUpdated(string $scriptName, string $requestUri, string $expectedPath, bool $onLogin): void
    {
        $cookie = new TestCookie();
        $cookie->admin_path = '/previous-admin/';
        $employee = $this->createMock(Employee::class);
        $employee->method('getId')->willReturn(42);
        $employee->method('getDefaultLanguage')->willReturn($this->createMock(Lang::class));
        $employee->method('getProfile')->willReturn($this->createMock(Profile::class));
        $employee->method('hasSession')->with(7, 'session-token')->willReturn(true);
        $session = $this->createMock(EmployeeSession::class);
        $session->method('getId')->willReturn(7);
        $session->method('getToken')->willReturn('session-token');
        $token = new UsernamePasswordToken($employee, 'main');
        $token->setAttribute(TokenAttributes::EMPLOYEE_SESSION, $session);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($employee);
        $security->method('getToken')->willReturn($token);
        $subscriber = $this->createSubscriber($cookie, $security);
        $request = Request::create('https://example.test' . $requestUri, 'GET', [], [], [], [
            'PHP_SELF' => $scriptName,
            'REMOTE_ADDR' => '127.0.0.1',
            'SCRIPT_FILENAME' => '/var/www' . $scriptName,
            'SCRIPT_NAME' => $scriptName,
        ]);

        if ($onLogin) {
            $event = $this->createMock(LoginSuccessEvent::class);
            $event->method('getRequest')->willReturn($request);
            $event->method('getResponse')->willReturn(null);
            $subscriber->onLoginSuccess($event);
        } else {
            $subscriber->onKernelRequest(new RequestEvent(
                $this->createMock(HttpKernelInterface::class),
                $request,
                HttpKernelInterface::MAIN_REQUEST
            ));
        }

        self::assertSame($expectedPath, $cookie->admin_path);
        self::assertSame(42, $cookie->id_employee);
        self::assertSame(7, $cookie->session_id);
        self::assertSame('session-token', $cookie->session_token);
        self::assertGreaterThanOrEqual(time() - 5, $token->getAttribute(TokenAttributes::LAST_ADMIN_ACTIVITY));
        self::assertLessThanOrEqual(time(), $token->getAttribute(TokenAttributes::LAST_ADMIN_ACTIVITY));
    }

    public static function getAdminRequests(): iterable
    {
        $requests = [
            'root legacy' => ['/adminXYZ/index.php', '/adminXYZ/index.php?controller=AdminOrders', '/adminXYZ/'],
            'subdirectory legacy' => ['/shop/adminXYZ/index.php', '/shop/adminXYZ/index.php?controller=AdminOrders', '/shop/adminXYZ/'],
            'subdirectory Symfony' => ['/shop/adminXYZ/index.php', '/shop/adminXYZ/index.php/sell/catalog/products', '/shop/adminXYZ/'],
            'rewritten Symfony' => ['/shop/adminXYZ/index.php', '/shop/adminXYZ/sell/catalog/products', '/shop/adminXYZ/'],
            'renamed admin' => ['/shop/renamed-admin/index.php', '/shop/renamed-admin/index.php', '/shop/renamed-admin/'],
        ];

        foreach ($requests as $name => $request) {
            yield $name . ' on login' => [...$request, true];
            yield $name . ' on request' => [...$request, false];
        }
    }

    public function testAnonymousRequestDoesNotPopulateAdminPath(): void
    {
        $cookie = new TestCookie();
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);
        $subscriber = $this->createSubscriber($cookie, $security);

        $subscriber->onKernelRequest(new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            Request::create('https://example.test/adminXYZ/index.php'),
            HttpKernelInterface::MAIN_REQUEST
        ));

        self::assertFalse(isset($cookie->admin_path));
    }

    private function createSubscriber(TestCookie $cookie, Security $security): EmployeeSessionSubscriber
    {
        $context = $this->createMock(Context::class);
        $context->cookie = $cookie;
        $legacyContext = $this->createMock(LegacyContext::class);
        $legacyContext->method('getContext')->willReturn($context);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('PS_COOKIE_CHECKIP')->willReturn(false);

        return new EmployeeSessionSubscriber(
            $this->createMock(EmployeeProvider::class),
            $this->createMock(EmployeeRepository::class),
            $this->createMock(EntityManagerInterface::class),
            $security,
            new NullLogger(),
            $legacyContext,
            $this->createMock(CsrfTokenManagerInterface::class),
            $this->createMock(RouterInterface::class),
            $configuration,
            $this->createMock(TranslatorInterface::class),
            $this->createMock(EmployeeContextBuilder::class),
            $this->createConfiguredMock(FeatureFlagStateCheckerInterface::class, ['isEnabled' => true])
        );
    }
}
