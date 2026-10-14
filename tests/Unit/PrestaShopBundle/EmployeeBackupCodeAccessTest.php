<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\TotMailer\TwoFactorAuthCodePrestashopMailer;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\Employee as ContextEmployee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters\EmployeeController;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\EmployeeBackupCodeManager;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use ReflectionClass;
use Scheb\TwoFactorBundle\Model\PersisterInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EmployeeBackupCodeAccessTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function backupCodeActions(): iterable
    {
        yield 'generate' => ['generateBackupCodesAction'];
        yield 'confirm regeneration' => ['confirmRegenerateBackupCodesAction'];
        yield 'regenerate' => ['regenerateBackupCodesAction'];
    }

    #[DataProvider('backupCodeActions')]
    public function testActionsAllowAnyAuthenticatedEmployeeRole(string $methodName): void
    {
        $attribute = (new ReflectionClass(EmployeeController::class))->getMethod($methodName)->getAttributes(AdminSecurity::class)[0]->newInstance();

        self::assertSame("is_granted('ROLE_EMPLOYEE')", $attribute->getAttribute());
    }

    #[DataProvider('backupCodeActions')]
    public function testActionsRejectAnotherEmployeesCodes(string $methodName): void
    {
        [$controller, $arguments, $repository] = $this->createActionCase($methodName, 8, false);
        $repository->expects(self::never())->method('findOneBy');

        $response = $controller->$methodName(...$arguments);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/admin_employees_index', $response->getTargetUrl());
    }

    public function testEmployeeCanGenerateOwnCodesWithoutTeamPermissions(): void
    {
        [$controller, $arguments, $repository, $employee] = $this->createActionCase('generateBackupCodesAction', 7, false);
        $repository->expects(self::once())->method('findOneBy')->willReturn($employee);

        $response = $controller->generateBackupCodesAction(...$arguments);

        self::assertSame('/admin_employees_edit', $response->getTargetUrl());
        self::assertCount(8, $employee->getTwoFactorBackupCodes());
    }

    public function testEmployeeCanOpenOwnRegenerationConfirmation(): void
    {
        [$controller, $arguments, $repository, $employee] = $this->createActionCase('confirmRegenerateBackupCodesAction', 7, true);
        $repository->expects(self::once())->method('findOneBy')->willReturn($employee);

        $response = $controller->confirmRegenerateBackupCodesAction(...$arguments);

        self::assertSame('confirmation', $response->getContent());
        self::assertNull($controller->renderParameters['authenticationError']);
    }

    public function testOwnRegenerationRequiresValidSecondFactor(): void
    {
        [$controller, $arguments, $repository, $employee] = $this->createActionCase('regenerateBackupCodesAction', 7, true, 'wrong');
        $repository->method('findOneBy')->willReturn($employee);

        $response = $controller->regenerateBackupCodesAction(...$arguments);

        self::assertSame('confirmation', $response->getContent());
        self::assertSame('code_invalid', $controller->renderParameters['authenticationError']);
        self::assertSame(['old-hash'], $employee->getTwoFactorBackupCodes());
    }

    public function testOwnRegenerationReplacesCodesAfterSecondFactor(): void
    {
        [$controller, $arguments, $repository, $employee] = $this->createActionCase('regenerateBackupCodesAction', 7, true);
        $repository->method('findOneBy')->willReturn($employee);

        $response = $controller->regenerateBackupCodesAction(...$arguments);

        self::assertSame('/admin_employees_edit', $response->getTargetUrl());
        self::assertCount(8, $employee->getTwoFactorBackupCodes());
        self::assertNotContains('old-hash', $employee->getTwoFactorBackupCodes());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function csrfProtectedActions(): iterable
    {
        yield 'generate' => ['generateBackupCodesAction'];
        yield 'regenerate' => ['regenerateBackupCodesAction'];
    }

    #[DataProvider('csrfProtectedActions')]
    public function testWriteActionsRejectInvalidCsrfToken(string $methodName): void
    {
        [$controller, $arguments, $repository] = $this->createActionCase($methodName, 7, true, '654321', 'invalid');
        $repository->expects(self::never())->method('findOneBy');

        $this->expectException(AccessDeniedException::class);
        $controller->$methodName(...$arguments);
    }

    /**
     * @return array{EmployeeController, array, EmployeeRepository, Employee}
     */
    private function createActionCase(string $methodName, int $targetId, bool $hasCodes, string $authCode = '654321', string $csrfToken = 'valid'): array
    {
        $contextEmployee = $this->createMock(ContextEmployee::class);
        $contextEmployee->method('getId')->willReturn(7);
        $controller = new class(new EmployeeContext($contextEmployee, [])) extends EmployeeController {
            public array $renderParameters = [];

            public function __construct(private readonly EmployeeContext $employeeContext)
            {
            }

            protected function getEmployeeContext(): EmployeeContext
            {
                return $this->employeeContext;
            }

            protected function isCsrfTokenValid(string $id, ?string $token): bool
            {
                return $token === 'valid';
            }

            protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
            {
                return new RedirectResponse('/' . $route, $status);
            }

            protected function generateUrl(string $route, array $parameters = [], int $referenceType = 1): string
            {
                return '/' . $route;
            }

            protected function generateSidebarLink(string $section, ?string $title = null): string
            {
                return '';
            }

            protected function render(string $view, array $parameters = [], ?Response $response = null): Response
            {
                $this->renderParameters = $parameters;

                return new Response('confirmation');
            }

            protected function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return $id;
            }

            protected function addFlash(string $type, mixed $message): void
            {
            }
        };

        $employee = new Employee();
        $employee->setTwoFactorEnabled(true);
        $employee->setTwoFactorEmailEnabled(true);
        $employee->setEmailAuthCode('654321');
        $employee->setTwoFactorBackupCodes($hasCodes ? ['old-hash'] : null);

        $request = new Request(['preferProvider' => 'email'], [
            '_token_generate_backup_codes' => $csrfToken,
            '_token' => $csrfToken,
            '_auth_code' => $authCode,
        ], ['_legacy_controller' => 'AdminEmployees']);
        $accessChecker = $this->createMock(EmployeeFormAccessCheckerInterface::class);
        $accessChecker->method('isRestrictedAccess')->willReturn($targetId === 7);
        $repository = $this->createMock(EmployeeRepository::class);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn(true);
        $hasher = $this->createMock(PasswordHasherInterface::class);
        $hasher->method('hash')->willReturnCallback(static fn (string $code): string => 'hash-' . $code);
        $manager = new EmployeeBackupCodeManager($hasher, $this->createMock(PersisterInterface::class));
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $mailer = (new ReflectionClass(TwoFactorAuthCodePrestashopMailer::class))->newInstanceWithoutConstructor();
        $userTokenManager = $this->createMock(UserTokenManager::class);
        $userTokenManager->method('getSymfonyToken')->willReturn('query-token');

        $arguments = match ($methodName) {
            'generateBackupCodesAction' => [$targetId, $request, $accessChecker, $repository, $manager, $entityManager, $configuration],
            'confirmRegenerateBackupCodesAction' => [$targetId, $request, $accessChecker, $repository, $configuration, $mailer, $userTokenManager, $entityManager],
            'regenerateBackupCodesAction' => [$targetId, $request, $accessChecker, $repository, $manager, $entityManager, $configuration, $this->createMock(TotpAuthenticatorInterface::class), $mailer, $userTokenManager],
        };

        return [$controller, $arguments, $repository, $employee];
    }
}
