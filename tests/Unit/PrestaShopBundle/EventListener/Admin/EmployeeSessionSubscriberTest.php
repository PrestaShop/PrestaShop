<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Session\Repository\CustomerSessionRepository;
use PrestaShop\PrestaShop\Adapter\Session\Repository\EmployeeSessionRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\EmployeeSession;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\EventListener\Admin\EmployeeSessionSubscriber;
use PrestaShopBundle\Security\Admin\EmployeeProvider;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Http\Authenticator\RememberMeAuthenticator;
use Symfony\Component\Security\Http\Event\AuthenticationTokenCreatedEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmployeeSessionSubscriberTest extends TestCase
{
    private const SESSION_LAST_CLEANUP_CONFIGURATION_KEY = 'PS_SESSION_LAST_CLEANUP';
    private const SESSION_CLEANUP_INTERVAL = 86400;
    private const EMPLOYEE_ID = 42;
    private const CLIENT_IP = '192.0.2.1';

    /** @var ShopConfigurationInterface&MockObject */
    private ShopConfigurationInterface $shopConfiguration;

    /** @var EmployeeSessionRepository&MockObject */
    private EmployeeSessionRepository $employeeSessionRepository;

    /** @var CustomerSessionRepository&MockObject */
    private CustomerSessionRepository $customerSessionRepository;

    /** @var LoggerInterface&MockObject */
    private LoggerInterface $logger;

    private EmployeeSessionSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->shopConfiguration = $this->createMock(ShopConfigurationInterface::class);
        $this->employeeSessionRepository = $this->createMock(EmployeeSessionRepository::class);
        $this->customerSessionRepository = $this->createMock(CustomerSessionRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testSuccessfulFormLoginIsLogged(): void
    {
        $request = Request::create(
            '/admin/login',
            'POST',
            [],
            [],
            [],
            ['REMOTE_ADDR' => self::CLIENT_IP]
        );

        $employee = $this->createMock(Employee::class);
        $employee
            ->method('getId')
            ->willReturn(self::EMPLOYEE_ID)
        ;

        $authenticator = $this->createMock(FormLoginAuthenticator::class);

        $event = $this->createMock(LoginSuccessEvent::class);
        $event
            ->method('getResponse')
            ->willReturn(null)
        ;
        $event
            ->method('getRequest')
            ->willReturn($request)
        ;
        $event
            ->method('getAuthenticator')
            ->willReturn($authenticator)
        ;
        $event
            ->method('getUser')
            ->willReturn($employee)
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects(static::once())
            ->method('trans')
            ->with(
                'Back office connection from %ip%',
                ['%ip%' => self::CLIENT_IP],
                'Admin.Advparameters.Feature'
            )
            ->willReturn('Back office connection from ' . self::CLIENT_IP)
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(static::once())
            ->method('info')
            ->with(
                'Back office connection from ' . self::CLIENT_IP,
                [
                    'allow_duplicate' => true,
                    'id_employee' => self::EMPLOYEE_ID,
                ]
            )
        ;

        $subscriber = $this->createSubscriber($translator, $logger);
        $subscriber
            ->expects(static::once())
            ->method('updateLegacyCookie')
            ->with(
                static::isInstanceOf(Request::class),
                true
            )
        ;

        $subscriber->onLoginSuccess($event);
    }

    public function testNonFormLoginIsNotLogged(): void
    {
        $request = Request::create(
            '/admin/login',
            'GET',
            [],
            [],
            [],
            ['REMOTE_ADDR' => self::CLIENT_IP]
        );

        $event = $this->createMock(LoginSuccessEvent::class);
        $event
            ->method('getResponse')
            ->willReturn(null)
        ;
        $event
            ->method('getRequest')
            ->willReturn($request)
        ;
        $event
            ->method('getAuthenticator')
            ->willReturn($this->createMock(RememberMeAuthenticator::class))
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects(static::never())
            ->method('trans')
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(static::never())
            ->method('info')
        ;

        $subscriber = $this->createSubscriber($translator, $logger);
        $subscriber
            ->expects(static::once())
            ->method('updateLegacyCookie')
            ->with(
                static::isInstanceOf(Request::class),
                true
            )
        ;

        $subscriber->onLoginSuccess($event);
    }

    public function testFormLoginWithNonEmployeeUserIsNotLogged(): void
    {
        $request = Request::create(
            '/admin/login',
            'POST',
            [],
            [],
            [],
            ['REMOTE_ADDR' => self::CLIENT_IP]
        );

        $authenticator = $this->createMock(FormLoginAuthenticator::class);
        $user = $this->createMock(UserInterface::class);

        $event = $this->createMock(LoginSuccessEvent::class);
        $event
            ->method('getResponse')
            ->willReturn(null)
        ;
        $event
            ->method('getRequest')
            ->willReturn($request)
        ;
        $event
            ->method('getAuthenticator')
            ->willReturn($authenticator)
        ;
        $event
            ->method('getUser')
            ->willReturn($user)
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects(static::never())
            ->method('trans')
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(static::never())
            ->method('info')
        ;

        $subscriber = $this->createSubscriber($translator, $logger);
        $subscriber
            ->expects(static::once())
            ->method('updateLegacyCookie')
            ->with(
                static::isInstanceOf(Request::class),
                true
            )
        ;

        $subscriber->onLoginSuccess($event);
    }

    public function testCleanupIsSkippedWhenLastSuccessfulCleanupWasLessThan24HoursAgo(): void
    {
        $this->subscriber = $this->createSessionCleanupSubscriber();

        $this->shopConfiguration
            ->expects($this->once())
            ->method('get')
            ->with(
                self::SESSION_LAST_CLEANUP_CONFIGURATION_KEY,
                0,
                $this->isGlobalShopConstraint(),
            )
            ->willReturn(time() - 3600);

        $this->employeeSessionRepository
            ->expects($this->never())
            ->method('clearOutdatedSessions');
        $this->customerSessionRepository
            ->expects($this->never())
            ->method('clearOutdatedSessions');
        $this->shopConfiguration
            ->expects($this->never())
            ->method('set');

        $this->subscriber->createEmployeeSession($this->createAuthenticationTokenCreatedEvent());
    }

    public function testBothSessionTypesAreCleanedWhenNoPreviousCleanupExists(): void
    {
        $this->subscriber = $this->createSessionCleanupSubscriber();

        $this->shopConfiguration
            ->expects($this->once())
            ->method('get')
            ->with(
                self::SESSION_LAST_CLEANUP_CONFIGURATION_KEY,
                0,
                $this->isGlobalShopConstraint(),
            )
            ->willReturn(0);

        $this->employeeSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');
        $this->customerSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');

        $this->expectLastCleanupTimestampToBeUpdated();

        $this->subscriber->createEmployeeSession($this->createAuthenticationTokenCreatedEvent());
    }

    public function testBothSessionTypesAreCleanedAfter24Hours(): void
    {
        $this->subscriber = $this->createSessionCleanupSubscriber();

        $this->shopConfiguration
            ->expects($this->once())
            ->method('get')
            ->with(
                self::SESSION_LAST_CLEANUP_CONFIGURATION_KEY,
                0,
                $this->isGlobalShopConstraint(),
            )
            ->willReturn(time() - self::SESSION_CLEANUP_INTERVAL - 1);

        $this->employeeSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');
        $this->customerSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');

        $this->expectLastCleanupTimestampToBeUpdated();

        $this->subscriber->createEmployeeSession($this->createAuthenticationTokenCreatedEvent());
    }

    public function testCustomerCleanupIsStillAttemptedWhenEmployeeCleanupFails(): void
    {
        $this->subscriber = $this->createSessionCleanupSubscriber();

        $exception = new RuntimeException('Employee cleanup failed');

        $this->shopConfiguration
            ->method('get')
            ->willReturn(0);

        $this->employeeSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions')
            ->willThrowException($exception);
        $this->customerSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');

        $this->shopConfiguration
            ->expects($this->never())
            ->method('set');

        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Failed to clear outdated employee sessions.',
                ['exception' => $exception],
            );

        $this->subscriber->createEmployeeSession($this->createAuthenticationTokenCreatedEvent());
    }

    public function testLastCleanupTimestampIsNotUpdatedWhenCustomerCleanupFails(): void
    {
        $this->subscriber = $this->createSessionCleanupSubscriber();

        $exception = new RuntimeException('Customer cleanup failed');

        $this->shopConfiguration
            ->method('get')
            ->willReturn(0);

        $this->employeeSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');
        $this->customerSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions')
            ->willThrowException($exception);

        $this->shopConfiguration
            ->expects($this->never())
            ->method('set');

        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Failed to clear outdated customer sessions.',
                ['exception' => $exception],
            );

        $this->subscriber->createEmployeeSession($this->createAuthenticationTokenCreatedEvent());
    }

    public function testCleanupIsAttemptedWhenLastCleanupTimestampCannotBeRead(): void
    {
        $this->subscriber = $this->createSessionCleanupSubscriber();

        $exception = new RuntimeException('Configuration read failed');

        $this->shopConfiguration
            ->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $this->employeeSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');
        $this->customerSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');

        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Failed to read the last session cleanup time.',
                ['exception' => $exception],
            );

        $this->expectLastCleanupTimestampToBeUpdated();

        $this->subscriber->createEmployeeSession($this->createAuthenticationTokenCreatedEvent());
    }

    public function testLoginContinuesWhenLastCleanupTimestampCannotBeUpdated(): void
    {
        $this->subscriber = $this->createSessionCleanupSubscriber();

        $exception = new RuntimeException('Configuration write failed');

        $this->shopConfiguration
            ->method('get')
            ->willReturn(0);

        $this->employeeSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');
        $this->customerSessionRepository
            ->expects($this->once())
            ->method('clearOutdatedSessions');

        $this->shopConfiguration
            ->expects($this->once())
            ->method('set')
            ->willThrowException($exception);

        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Failed to update the last session cleanup time.',
                ['exception' => $exception],
            );

        $this->subscriber->createEmployeeSession($this->createAuthenticationTokenCreatedEvent());
    }

    private function createSessionCleanupSubscriber(): EmployeeSessionSubscriber
    {
        $employee = $this->createMock(Employee::class);
        $employee
            ->method('getId')
            ->willReturn(42);
        $employee
            ->expects($this->once())
            ->method('addSession')
            ->with($this->isInstanceOf(EmployeeSession::class))
            ->willReturnSelf();

        $employeeRepository = $this->createMock(EmployeeRepository::class);
        $employeeRepository
            ->expects($this->once())
            ->method('loadEmployeeByIdentifier')
            ->with('admin@example.com')
            ->willReturn($employee);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(EmployeeSession::class));
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $employeeContextBuilder = $this->createMock(EmployeeContextBuilder::class);
        $employeeContextBuilder
            ->expects($this->once())
            ->method('setEmployeeId')
            ->with(42)
            ->willReturnSelf();

        return new EmployeeSessionSubscriber(
            $this->createStub(EmployeeProvider::class),
            $employeeRepository,
            $entityManager,
            $this->createStub(Security::class),
            $this->logger,
            $this->createStub(LegacyContext::class),
            $this->createStub(CsrfTokenManagerInterface::class),
            $this->createStub(RouterInterface::class),
            $this->createStub(ConfigurationInterface::class),
            $this->createStub(TranslatorInterface::class),
            $employeeContextBuilder,
            $this->shopConfiguration,
            $this->employeeSessionRepository,
            $this->customerSessionRepository,
        );
    }

    private function createSubscriber(
        TranslatorInterface $translator,
        LoggerInterface $logger,
    ): EmployeeSessionSubscriber|MockObject {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration
            ->method('get')
            ->with('PS_COOKIE_CHECKIP')
            ->willReturn(false)
        ;

        $subscriber = $this
            ->getMockBuilder(EmployeeSessionSubscriber::class)
            ->setConstructorArgs([
                $this->createMock(EmployeeProvider::class),
                $this->createMock(EmployeeRepository::class),
                $this->createMock(EntityManagerInterface::class),
                $this->createMock(Security::class),
                $logger,
                $this->createMock(LegacyContext::class),
                $this->createMock(CsrfTokenManagerInterface::class),
                $this->createMock(RouterInterface::class),
                $configuration,
                $translator,
                $this->createMock(EmployeeContextBuilder::class),
                $this->createMock(ShopConfigurationInterface::class),
                $this->createMock(EmployeeSessionRepository::class),
                $this->createMock(CustomerSessionRepository::class),
            ])
            ->onlyMethods(['updateLegacyCookie'])
            ->getMock()
        ;

        return $subscriber;
    }

    private function createAuthenticationTokenCreatedEvent(): AuthenticationTokenCreatedEvent
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUserIdentifier')
            ->willReturn('admin@example.com');
        $token
            ->expects($this->once())
            ->method('setAttribute')
            ->with(
                TokenAttributes::EMPLOYEE_SESSION,
                $this->isInstanceOf(EmployeeSession::class),
            );

        $event = $this->createMock(AuthenticationTokenCreatedEvent::class);
        $event
            ->method('getAuthenticatedToken')
            ->willReturn($token);

        return $event;
    }

    private function expectLastCleanupTimestampToBeUpdated(): void
    {
        $this->shopConfiguration
            ->expects($this->once())
            ->method('set')
            ->with(
                self::SESSION_LAST_CLEANUP_CONFIGURATION_KEY,
                $this->callback(
                    static fn (mixed $timestamp): bool => is_int($timestamp) && abs(time() - $timestamp) <= 2
                ),
                $this->isGlobalShopConstraint(),
            )
            ->willReturnSelf();
    }

    private function isGlobalShopConstraint(): Callback
    {
        return $this->callback(
            static fn (mixed $constraint): bool => $constraint instanceof ShopConstraint
                && $constraint->forAllShops()
        );
    }
}
