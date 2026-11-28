<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\GenerateEmployeeBackupCodesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\InitializeEmployeeTotpSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\PrepareEmployeeBackupCodeRegenerationCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\RegenerateEmployeeBackupCodesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeTwoFactorSettings;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EmployeeTwoFactorSettings;
use PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters\EmployeeController;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EmployeeTwoFactorControllerTest extends TestCase
{
    #[DataProvider('postActions')]
    public function testEveryWriteRequiresItsOwnCsrfTokenFromPostBody(string $action): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->expects(self::never())->method('handle');
        $csrf = $this->createMock(CsrfTokenManagerInterface::class);
        $csrf->expects(self::once())->method('isTokenValid')->willReturn(false);
        $container = new Container();
        $container->set(CommandBusInterface::class, $bus);
        $container->set('security.csrf.token_manager', $csrf);
        $controller = new EmployeeController();
        $controller->setContainer($container);
        // The admin URL token is insufficient for the independent POST operation.
        $request = Request::create('/employee/42?_token=url-token', 'POST');
        $this->expectException(AccessDeniedException::class);
        $arguments = [42, $request];
        if ('regenerateBackupCodesAction' === $action) {
            $arguments[] = $this->createMock(UserTokenManager::class);
        }
        $controller->$action(...$arguments);
    }

    public static function postActions(): iterable
    {
        foreach (['initializeTotpSecretAction', 'generateBackupCodesAction', 'prepareRegenerateBackupCodesAction', 'regenerateBackupCodesAction'] as $action) {
            yield $action => [$action];
        }
    }

    public function testInitializationDispatchesWriteThenReadAndDisablesResponseCaching(): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $calls = 0;
        $bus->expects(self::exactly(2))->method('handle')->willReturnCallback(static function (object $message) use (&$calls) {
            ++$calls;
            self::assertSame(42, $message->getEmployeeId()->getValue());
            if (1 === $calls) {
                self::assertInstanceOf(InitializeEmployeeTotpSecretCommand::class, $message);

                return null;
            }
            self::assertInstanceOf(GetEmployeeTwoFactorSettings::class, $message);

            return new EmployeeTwoFactorSettings(false, false, [], '', 'pending-secret', 'data:image/png;base64,qr');
        });
        $csrf = $this->createMock(CsrfTokenManagerInterface::class);
        $csrf->method('isTokenValid')->willReturn(true);
        $container = new Container();
        $container->set(CommandBusInterface::class, $bus);
        $container->set('security.csrf.token_manager', $csrf);
        $controller = new EmployeeController();
        $controller->setContainer($container);
        $response = $controller->initializeTotpSecretAction(42, Request::create('/employee/42', 'POST', ['_token_initialize_totp' => 'csrf-token'], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']));
        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($response->headers->hasCacheControlDirective('no-store'));
        self::assertSame('pending-secret', json_decode($response->getContent(), true)['totpSecret']);
    }

    public function testNonJavascriptInitializationRedirectsWithoutExposingTheSecret(): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->expects(self::once())->method('handle')->with(self::isInstanceOf(InitializeEmployeeTotpSecretCommand::class));
        [$controller] = $this->createController($bus);
        $response = $controller->initializeTotpSecretAction(42, Request::create('/employee/42', 'POST', ['_token_initialize_totp' => 'csrf-token']));
        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/employee/42', $response->headers->get('Location'));
        self::assertStringNotContainsString('totpSecret', $response->getContent());
    }

    public function testPreparationFailureInsideGenerateCatchIsHandled(): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->expects(self::exactly(2))->method('handle')->willReturnCallback(static function (object $command) {
            if ($command instanceof GenerateEmployeeBackupCodesCommand) {
                throw new EmployeeTwoFactorException('Already generated', EmployeeTwoFactorException::CONFIRMATION_REQUIRED);
            }
            self::assertInstanceOf(PrepareEmployeeBackupCodeRegenerationCommand::class, $command);
            throw new EmployeeTwoFactorException('Disabled concurrently', EmployeeTwoFactorException::DISABLED);
        });
        [$controller, $session] = $this->createController($bus);
        $response = $controller->generateBackupCodesAction(42, Request::create('/employee/42', 'POST', ['_token_generate_backup_codes' => 'csrf-token']));
        self::assertSame('/employee/42', $response->headers->get('Location'));
        self::assertCount(1, $session->getFlashBag()->peek('error'));
        self::assertSame([], $session->getFlashBag()->peek('backup_codes'));
    }

    public function testNonJavascriptInitializationFailurePreservesTheRedirectFlow(): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->method('handle')->willThrowException(new EmployeeTwoFactorException('Not owner', EmployeeTwoFactorException::NOT_SELF));
        [$controller, $session] = $this->createController($bus);
        $response = $controller->initializeTotpSecretAction(42, Request::create('/employee/42', 'POST', ['_token_initialize_totp' => 'csrf-token']));
        self::assertSame('/employee/42', $response->headers->get('Location'));
        self::assertCount(1, $session->getFlashBag()->peek('error'));
    }

    public function testConcurrentGenerationFailureNeverFlashesBackupCodes(): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->expects(self::once())->method('handle')->willThrowException(new EmployeeTwoFactorException('Conflict', EmployeeTwoFactorException::CONCURRENT_UPDATE));
        [$controller, $session] = $this->createController($bus);
        $response = $controller->generateBackupCodesAction(42, Request::create('/employee/42', 'POST', ['_token_generate_backup_codes' => 'csrf-token']));
        self::assertSame('/employee/42', $response->headers->get('Location'));
        self::assertSame(['Your two-factor authentication settings changed during this operation. Reload your profile and try again.'], $session->getFlashBag()->peek('error'));
        self::assertSame([], $session->getFlashBag()->peek('backup_codes'));
    }

    public function testInvalidRegenerationCodeRendersTheExistingWordingWithoutFlashingCodes(): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->expects(self::exactly(2))->method('handle')->willReturnCallback(static function (object $message) {
            if ($message instanceof RegenerateEmployeeBackupCodesCommand) {
                throw new EmployeeTwoFactorException('Invalid code', EmployeeTwoFactorException::INVALID_CODE);
            }
            self::assertInstanceOf(GetEmployeeTwoFactorSettings::class, $message);

            return new EmployeeTwoFactorSettings(true, true, ['totp'], 'totp', '', '');
        });
        $controller = $this->getMockBuilder(EmployeeController::class)->onlyMethods(['generateSidebarLink'])->getMock();
        $controller->expects(self::once())->method('generateSidebarLink')->with('AdminEmployees')->willReturn('/help');
        [$controller, $session, $container] = $this->createController($bus, $controller);
        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())->method('render')->willReturnCallback(static function (string $view, array $parameters): string {
            self::assertSame('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/confirm_regenerate_backup_codes.html.twig', $view);
            self::assertSame('The verification code is not valid.', $parameters['authenticationError']);
            self::assertSame([], $parameters['authenticationErrorData']);

            return 'Confirmation with invalid code';
        });
        $container->set('twig', $twig);
        $tokens = $this->createMock(UserTokenManager::class);
        $tokens->method('getSymfonyToken')->willReturn('admin-token');
        $request = Request::create('/employee/42?preferProvider=totp', 'POST', ['_token' => 'csrf-token', '_auth_code' => 'invalid']);
        $request->attributes->set('_legacy_controller', 'AdminEmployees');

        $response = $controller->regenerateBackupCodesAction(42, $request, $tokens);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('Confirmation with invalid code', $response->getContent());
        self::assertSame([], $session->getFlashBag()->peek('backup_codes'));
        self::assertSame([], $session->getFlashBag()->peek('error'));
    }

    private function createController(CommandBusInterface $bus, ?EmployeeController $controller = null): array
    {
        $csrf = $this->createMock(CsrfTokenManagerInterface::class);
        $csrf->method('isTokenValid')->willReturn(true);
        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturn('/employee/42');
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static function (string $message): string {
            self::assertNotSame('code_invalid', $message);

            return $message;
        });
        $session = new Session(new MockArraySessionStorage());
        $request = Request::create('/employee/42');
        $request->setSession($session);
        $stack = new RequestStack();
        $stack->push($request);
        $container = new Container();
        $container->set(CommandBusInterface::class, $bus);
        $container->set('security.csrf.token_manager', $csrf);
        $container->set('router', $router);
        $container->set('request_stack', $stack);
        $container->set(TranslatorInterface::class, $translator);
        $controller ??= new EmployeeController();
        $controller->setContainer($container);

        return [$controller, $session, $container];
    }
}
