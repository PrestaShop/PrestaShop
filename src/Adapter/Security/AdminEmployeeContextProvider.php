<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security;

use BadMethodCallException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Error;
use ErrorException;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use PrestaShop\PrestaShop\Core\Security\AdminEmployeeContext;
use PrestaShop\PrestaShop\Core\Security\AdminSessionReaderInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\EmployeeSession;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

/**
 * Provides a validated Back Office employee context to the Front Office.
 *
 * It reads the shared PHP session without extending its lifetime and returns
 * only the employee and profile identifiers required by the Admin Bar.
 */
final class AdminEmployeeContextProvider
{
    private const MAX_SERIALIZED_TOKEN_LENGTH = 1048576;

    public function __construct(
        private readonly AdminSessionReaderInterface $sessionReader,
        private readonly ConfigurationInterface $configuration,
        private readonly Connection $connection,
        private readonly string $databasePrefix,
    ) {
    }

    /**
     * Returns the Back Office employee context only when the shared PHP session is still valid.
     *
     * Reading this context never refreshes the Back Office activity timestamp or session lifetime.
     */
    public function getContext(): ?AdminEmployeeContext
    {
        $session = $this->sessionReader->read();

        // Matches the "main" firewall in app/config/admin/security.yml.
        $serializedToken = $session['_sf2_attributes']['_security_main'] ?? null;
        if (!is_string($serializedToken) || $serializedToken === '' || strlen($serializedToken) > self::MAX_SERIALIZED_TOKEN_LENGTH) {
            return null;
        }

        $token = $this->readToken($serializedToken);
        if ($token === null || !$this->isLastAdminActivityValid($token)) {
            return null;
        }

        $employee = $token->getUser();
        if (!$employee instanceof Employee || $employee->getId() <= 0 || !$token->hasAttribute(TokenAttributes::EMPLOYEE_SESSION)) {
            return null;
        }
        $employeeSession = $token->getAttribute(TokenAttributes::EMPLOYEE_SESSION);
        if (!$employeeSession instanceof EmployeeSession || $employeeSession->getId() <= 0 || !$employeeSession->getToken()) {
            return null;
        }

        if ((bool) $this->configuration->get('PS_COOKIE_CHECKIP')) {
            if (!$token->hasAttribute(TokenAttributes::IP_ADDRESS)
                || !is_string($token->getAttribute(TokenAttributes::IP_ADDRESS))
                || $token->getAttribute(TokenAttributes::IP_ADDRESS) !== ($_SERVER['REMOTE_ADDR'] ?? null)) {
                return null;
            }
        }

        if (!$this->isEmployeeSessionValid($employee, $employeeSession)) {
            return null;
        }

        return new AdminEmployeeContext($employee->getId(), $employee->getProfile()->getId());
    }

    private function isLastAdminActivityValid(TokenInterface $token): bool
    {
        if (!$token->hasAttribute(TokenAttributes::LAST_ADMIN_ACTIVITY)) {
            return false;
        }

        $lastActivity = $token->getAttribute(TokenAttributes::LAST_ADMIN_ACTIVITY);
        if (is_string($lastActivity) && ctype_digit($lastActivity)) {
            $lastActivity = (int) $lastActivity;
        }

        $now = time();
        $backOfficeLifetimeHours = (int) $this->configuration->get('PS_COOKIE_LIFETIME_BO');
        $backOfficeCookieLifetime = ($backOfficeLifetimeHours > 0 ? min($backOfficeLifetimeHours, CookieOptions::MAX_COOKIE_VALUE) : CookieOptions::MAX_COOKIE_VALUE) * 3600;
        $storageLifetime = (int) ini_get('session.gc_maxlifetime');
        // Conservative FO expiry: do not rely on probabilistic PHP garbage collection.
        $lifetime = $storageLifetime > 0 ? min($backOfficeCookieLifetime, $storageLifetime) : $backOfficeCookieLifetime;

        return is_int($lastActivity)
            && $lastActivity > 0
            && $lastActivity <= $now
            && $now - $lastActivity < $lifetime;
    }

    private function isEmployeeSessionValid(Employee $employee, EmployeeSession $employeeSession): bool
    {
        try {
            // The Front Office container cannot instantiate EmployeeRepository because it does not load its
            // Back Office dependencies. Validate the employee and persisted Back Office session through a
            // DBAL query limited to the required data.
            $row = $this->connection->createQueryBuilder()
                ->select('e.id_employee')
                ->from($this->databasePrefix . 'employee', 'e')
                ->innerJoin('e', $this->databasePrefix . 'employee_session', 'es', 'es.id_employee = e.id_employee')
                ->where('e.id_employee = :employeeId')
                ->andWhere('e.email = :email')
                ->andWhere('e.passwd = :password')
                ->andWhere('e.id_profile = :profileId')
                ->andWhere('e.active = :active')
                ->andWhere('es.id_employee_session = :sessionId')
                ->andWhere('es.token = :sessionToken')
                ->setParameter('employeeId', $employee->getId())
                ->setParameter('email', $employee->getEmail())
                ->setParameter('password', $employee->getPassword())
                ->setParameter('profileId', $employee->getProfile()->getId())
                ->setParameter('active', true, \Doctrine\DBAL\ParameterType::BOOLEAN)
                ->setParameter('sessionId', $employeeSession->getId())
                ->setParameter('sessionToken', $employeeSession->getToken())
                ->setMaxResults(1)
                ->executeQuery()
                ->fetchAssociative()
            ;
        } catch (Exception) {
            return false;
        }

        return $row !== false;
    }

    private function readToken(string $serializedToken): ?TokenInterface
    {
        // Only deserialize the server-side security token, never an HTTP cookie.
        // Unknown/custom token types fail closed; unrelated attributes need no classes.
        set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
        try {
            $token = unserialize($serializedToken, [
                'allowed_classes' => [Employee::class, EmployeeSession::class, PostAuthenticationToken::class, RememberMeToken::class, UsernamePasswordToken::class],
                'max_depth' => 64,
            ]);
            if ((!$token instanceof PostAuthenticationToken && !$token instanceof RememberMeToken && !$token instanceof UsernamePasswordToken)
                || $token->getFirewallName() !== 'main') {
                return null;
            }

            return $token;
        } catch (ErrorException|Error|BadMethodCallException) {
            return null;
        } finally {
            restore_error_handler();
        }
    }
}
