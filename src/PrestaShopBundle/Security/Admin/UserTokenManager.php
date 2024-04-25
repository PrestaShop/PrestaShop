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

use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\Hashing;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * This service centralizes the generation and validation of the admin query token to avoid duplicating code and rely
 * on a single homogeneous implementation in the whole back office. It can generate symfony CSRF tokens (not legacy
 * ones so far because not needed but it could) and validate both legacy and CSRF tokens.
 */
class UserTokenManager
{
    /**
     * @var array
     */
    private $tokens = [];

    public function __construct(
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly EmployeeContext $employeeContext,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly MenuBuilder $menuBuilder,
        private readonly Hashing $hashing,
        private readonly string $cookieKey,
        private readonly RequestStack $requestStack,
        private readonly Security $security,
        private readonly SessionEmployeeProvider $sessionEmployeeProvider,
    ) {
    }

    public function getSymfonyToken(): string
    {
        // When user is logged we can get it from Security service
        if ($this->security->getUser()) {
            $userIdentifier = $this->security->getUser()->getUserIdentifier();
        } else {
            // When user is not initialized yet (like in LegacyRouterChecker) we fetch the employee based on saved session data
            $userIdentifier = $this->sessionEmployeeProvider->getEmployeeIdentifierFromSession();
        }

        if (empty($userIdentifier)) {
            return '';
        }

        // Do not generate token each time we need one, one token per request is enough and can be used for all generated URLs
        if (!isset($this->tokens[$userIdentifier])) {
            $this->tokens[$userIdentifier] = $this->tokenManager->getToken($userIdentifier)->getValue();
        }

        return $this->tokens[$userIdentifier];
    }

    public function isTokenValid(): bool
    {
        $request = $this->requestStack->getMainRequest();
        $symfonyLayoutEnabled = $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_SYMFONY_LAYOUT);

        // Check if legacy token is present and valid, but only when Symfony layout is disabled, when it's enabled the token is always a CSRF one
        if (!$symfonyLayoutEnabled && $this->isLegacyTokenValid($request)) {
            return true;
        }

        // Check usual CSRF _token value for migrated pages
        if ($request->query->has('_token') && $this->isCsrfTokenValid($request->query->get('_token'))) {
            return true;
        }

        // Legacy urls use token instead of _token as the URL parameter, so it's a valid alternative but only when symfony layout is enabled
        // Token can be posted via GET or POST parameters
        $legacyRequestToken = $request->get('token');
        if ($symfonyLayoutEnabled && !empty($legacyRequestToken) && $this->isCsrfTokenValid($legacyRequestToken)) {
            return true;
        }

        return false;
    }

    private function isCsrfTokenValid(string $tokenValue): bool
    {
        if (!$this->security->getUser()) {
            return false;
        }

        $token = new CsrfToken($this->security->getUser()->getUserIdentifier(), $tokenValue);

        return $this->tokenManager->isTokenValid($token);
    }

    private function isLegacyTokenValid(Request $request): bool
    {
        // Token and controllers can be posted via GET or POST parameters
        $legacyRequestToken = $request->get('token');
        $controllerName = $request->get('controller');
        if (empty($legacyRequestToken) || empty($controllerName)) {
            return false;
        }

        $employeeId = (int) $this->employeeContext->getEmployee()?->getId();
        $currentTabId = $this->menuBuilder->getCurrentTab()?->getId();
        $expectedLegacyTokenValue = $this->hashing->hash($controllerName . $currentTabId . $employeeId, $this->cookieKey);

        return $expectedLegacyTokenValue === $legacyRequestToken;
    }
}
