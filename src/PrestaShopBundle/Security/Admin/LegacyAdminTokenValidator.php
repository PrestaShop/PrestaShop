<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\Admin;

use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorageFactory;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * This service is used to validate the query token in legacy context, especially for Frontend.
 * It's called legacy because it's used in legacy context, but it can validate both Symfony and legacy tokens.
 * As such it's a common service for front and admin which is why some of its dependencies are built manually
 * and why we partially rely on legacy classes and tools.
 */
class LegacyAdminTokenValidator
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function isTokenValid(?int $employeeId = null, ?string $adminToken = null): bool
    {
        $adminToken = $this->getAdminToken($adminToken);
        if (empty($adminToken)) {
            return false;
        }

        $employeeId = $this->getEmployeeId($employeeId);
        if (empty($employeeId)) {
            return false;
        }

        return $this->isCsrfTokenValid($adminToken, $employeeId);
    }

    private function isCsrfTokenValid(string $tokenValue, int $employeeId): bool
    {
        try {
            $employee = $this->employeeRepository->get($employeeId);
        } catch (EmployeeNotFoundException) {
            return false;
        }

        $token = new CsrfToken($employee->email, $tokenValue);
        $csrfTokenManager = $this->buildCsrfTokenManager();

        return $csrfTokenManager->isTokenValid($token);
    }

    /**
     * We manually build the CsrfTokenManager so that it matches the configuration of the on used in the back-office. This way
     * the token storage has the same info and is able to validate the token based on the admin saved token.
     *
     * @return CsrfTokenManager
     */
    private function buildCsrfTokenManager(): CsrfTokenManager
    {
        $sessionFactory = new SessionFactory($this->requestStack, new NativeSessionStorageFactory());
        $session = $sessionFactory->createSession();
        $this->requestStack->getMainRequest()->setSession($session);

        $sessionTokenStorage = new SessionTokenStorage($this->requestStack);

        return new CsrfTokenManager(null, $sessionTokenStorage);
    }

    private function getEmployeeId(?int $employeeId): ?int
    {
        if (!empty($employeeId)) {
            return $employeeId;
        }

        return $this->requestStack->getMainRequest()->get('id_employee', null);
    }

    private function getAdminToken(?string $adminToken): ?string
    {
        if (!empty($adminToken)) {
            return $adminToken;
        }

        // Legacy admin token is passed via token parameter
        $token = $this->requestStack->getMainRequest()->get('token', null);
        if (null === $token) {
            // Symfony CSRF token is passed via _token parameter
            $token = $this->requestStack->getMainRequest()->get('_token', null);
        }
        if (null === $token) {
            // Frontend token (used for preview mode mostly) is passed via adtoken parameter
            $token = $this->requestStack->getMainRequest()->get('adtoken', null);
        }

        return $token;
    }
}
