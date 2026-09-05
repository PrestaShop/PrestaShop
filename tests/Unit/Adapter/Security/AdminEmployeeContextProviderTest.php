<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Security;

use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Security\AdminEmployeeContextProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Security\AdminSessionReaderInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\EmployeeSession;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use stdClass;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

final class AdminEmployeeContextProviderTest extends TestCase
{
    /**
     * @dataProvider getScenarios
     */
    public function testValidation(string $scenario, bool $expectedValid): void
    {
        $employee = $this->createEmployee();
        $session = new EmployeeSession();
        $session->__unserialize(['id' => 7, 'token' => 'session-token']);
        $employee->addSession($session);
        $token = match ($scenario) {
            'legacy token' => new UsernamePasswordToken($employee, 'main', [Employee::ROLE_EMPLOYEE]),
            'remember me token' => new RememberMeToken($employee, 'main', 'synthetic-secret'),
            default => new PostAuthenticationToken($employee, $scenario === 'other firewall' ? 'other' : 'main', [Employee::ROLE_EMPLOYEE]),
        };
        $token->setAttribute(TokenAttributes::EMPLOYEE_SESSION, $session);
        $token->setAttribute(TokenAttributes::LAST_ADMIN_ACTIVITY, time());
        $token->setAttribute(TokenAttributes::IP_ADDRESS, '2001:db8::1');

        if ($scenario === 'expired') {
            $token->setAttribute(TokenAttributes::LAST_ADMIN_ACTIVITY, time() - 86400);
        } elseif ($scenario === 'future timestamp') {
            $token->setAttribute(TokenAttributes::LAST_ADMIN_ACTIVITY, time() + 3600);
        } elseif ($scenario === 'missing activity') {
            $token->setAttributes([TokenAttributes::EMPLOYEE_SESSION => $session]);
        } elseif ($scenario === 'missing employee session') {
            $token->setAttributes([TokenAttributes::LAST_ADMIN_ACTIVITY => time()]);
        } elseif ($scenario === 'missing IP') {
            $attributes = $token->getAttributes();
            unset($attributes[TokenAttributes::IP_ADDRESS]);
            $token->setAttributes($attributes);
        }

        $serialized = serialize($token);
        $serverSession = ['_sf2_attributes' => ['_security_main' => $serialized]];
        if ($scenario === 'missing PHP session' || $scenario === 'legacy cookie only') {
            $serverSession = null;
        } elseif ($scenario === 'missing Symfony token') {
            $serverSession = ['_sf2_attributes' => []];
        } elseif ($scenario === 'malformed token') {
            $serverSession['_sf2_attributes']['_security_main'] = 'not serialized';
        } elseif ($scenario === 'non-token object') {
            $serverSession['_sf2_attributes']['_security_main'] = serialize(new stdClass());
        }

        $fresh = $this->createEmployee(
            $scenario === 'changed password' ? 'new-password-hash' : 'password-hash',
            $scenario === 'changed profile' ? 3 : 2,
            $scenario === 'changed email' ? 'other@example.test' : 'employee@example.test',
            $scenario === 'changed employee ID' ? 43 : 42
        );
        $fresh->setActive($scenario !== 'inactive employee');
        if ($scenario !== 'revoked session') {
            $freshSession = new EmployeeSession();
            $freshSession->__unserialize(['id' => 7, 'token' => $scenario === 'wrong session token' ? 'other-token' : 'session-token']);
            $fresh->addSession($freshSession);
        }

        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $connection->executeStatement('CREATE TABLE employee (id_employee INTEGER PRIMARY KEY, email VARCHAR(255), passwd VARCHAR(255), id_profile INTEGER, active INTEGER)');
        $connection->executeStatement('CREATE TABLE employee_session (id_employee_session INTEGER PRIMARY KEY, id_employee INTEGER, token VARCHAR(40))');
        if ($scenario !== 'deleted employee') {
            $connection->insert('employee', [
                'id_employee' => $fresh->getId(),
                'email' => $fresh->getEmail(),
                'passwd' => $fresh->getPassword(),
                'id_profile' => $fresh->getProfile()->getId(),
                'active' => $fresh->isActive(),
            ]);
        }
        if ($scenario !== 'deleted employee' && $scenario !== 'revoked session') {
            $connection->insert('employee_session', [
                'id_employee_session' => 7,
                'id_employee' => $fresh->getId(),
                'token' => $scenario === 'wrong session token' ? 'other-token' : 'session-token',
            ]);
        }

        $reader = $this->createMock(AdminSessionReaderInterface::class);
        $reader->method('read')->willReturn($serverSession);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnMap([
            ['PS_COOKIE_LIFETIME_BO', 1],
            ['PS_COOKIE_CHECKIP', $scenario !== 'IP check disabled'],
        ]);
        $provider = new AdminEmployeeContextProvider($reader, $configuration, $connection, '');
        $_SERVER['REMOTE_ADDR'] = in_array($scenario, ['changed IP', 'IP check disabled'], true) ? '2001:db8::2' : '2001:db8::1';
        if ($scenario === 'legacy cookie only') {
            $_COOKIE['psAdmin'] = 'presence-is-not-authentication';
        }

        $context = $provider->getContext();
        if (!$expectedValid) {
            self::assertNull($context);

            return;
        }
        self::assertNotNull($context);
        self::assertSame(42, $context->getEmployeeId());
        self::assertSame(2, $context->getProfileId());
        self::assertSame($serialized, $serverSession['_sf2_attributes']['_security_main']);
        self::assertNotNull($provider->getContext());
        self::assertSame($serialized, $serverSession['_sf2_attributes']['_security_main']);
    }

    public static function getScenarios(): iterable
    {
        foreach (['valid', 'legacy token', 'remember me token', 'IP check disabled'] as $scenario) {
            yield $scenario => [$scenario, true];
        }
        foreach (['expired', 'future timestamp', 'missing activity', 'missing employee session', 'missing IP', 'changed IP', 'missing PHP session', 'legacy cookie only', 'missing Symfony token', 'malformed token', 'non-token object', 'other firewall', 'inactive employee', 'deleted employee', 'revoked session', 'wrong session token', 'changed password', 'changed profile', 'changed email', 'changed employee ID'] as $scenario) {
            yield $scenario => [$scenario, false];
        }
    }

    private function createEmployee(string $password = 'password-hash', int $profileId = 2, string $email = 'employee@example.test', int $id = 42): Employee
    {
        $employee = new Employee();
        $employee->__unserialize([
            'id' => $id,
            'email' => $email,
            'password' => $password,
            'profileId' => $profileId,
            'defaultLocale' => 'en-US',
        ]);
        $employee->setActive(true);

        return $employee;
    }
}
