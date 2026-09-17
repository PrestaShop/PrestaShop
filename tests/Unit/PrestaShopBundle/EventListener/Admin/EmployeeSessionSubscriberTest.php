<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\EventListener\Admin\EmployeeSessionSubscriber;
use PrestaShopBundle\Security\Admin\EmployeeProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\RememberMeAuthenticator;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmployeeSessionSubscriberTest extends TestCase
{
    private const EMPLOYEE_ID = 42;
    private const CLIENT_IP = '192.0.2.1';

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
            ])
            ->onlyMethods(['updateLegacyCookie'])
            ->getMock()
        ;

        return $subscriber;
    }
}
