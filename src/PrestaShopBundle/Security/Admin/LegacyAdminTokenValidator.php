<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\Admin;

use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\Hashing;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorageFactory;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * This service is used to validate the query token in legacy context, especially for Frontend.
 * It's called legacy because it's used in legacy context but ti can validate both Symfony and legacy tokens.
 * As such it's a common service for front and admin which is why some of its dependencies are built manually
 * and why we partially rely on legacy classes and tools.
 */
class LegacyAdminTokenValidator
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly TabRepository $tabRepository,
        private readonly Hashing $hashing,
        private readonly string $cookieKey,
        private readonly RequestStack $requestStack,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    public function isTokenValid(?string $controllerName = null, ?int $employeeId = null, ?string $adminToken = null): bool
    {
        $adminToken = $this->getAdminToken($adminToken);
        if (empty($adminToken)) {
            return false;
        }

        $employeeId = $this->getEmployeeId($employeeId);
        if (empty($employeeId)) {
            return false;
        }

        $controllerName = $this->getControllerName($controllerName);
        if (!empty($controllerName) && $this->isLegacyTokenValid($adminToken, $controllerName, $employeeId)) {
            return true;
        }

        // CSRF token is only check when Symfony layout is enabled
        $symfonyLayoutEnabled = $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_SYMFONY_LAYOUT);
        if (!$symfonyLayoutEnabled) {
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

    private function isLegacyTokenValid(string $tokenValue, string $controllerName, int $employeeId): bool
    {
        $tab = $this->tabRepository->findOneByClassName($controllerName);
        $controllerTabId = $tab ? $tab->getId() : '';
        $expectedLegacyTokenValue = $this->hashing->hash($controllerName . $controllerTabId . $employeeId, $this->cookieKey);

        return $expectedLegacyTokenValue === $tokenValue;
    }

    private function getEmployeeId(?int $employeeId): ?int
    {
        if (!empty($employeeId)) {
            return $employeeId;
        }

        return $this->requestStack->getMainRequest()->get('id_employee', null);
    }

    private function getControllerName(?string $controllerName): ?string
    {
        if (!empty($controllerName)) {
            return $controllerName;
        }

        return $this->requestStack->getMainRequest()->get('controller', null);
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
