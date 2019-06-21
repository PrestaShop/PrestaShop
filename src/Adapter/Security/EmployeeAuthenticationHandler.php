<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Security;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EmployeeForAuthentication;
use PrestaShop\PrestaShop\Core\Security\EmployeeAuthenticationHandlerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Handles authentication credentials storage.
 */
final class EmployeeAuthenticationHandler implements EmployeeAuthenticationHandlerInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param LegacyContext $legacyContext
     * @param TokenStorageInterface $tokenStorage
     * @param UserProviderInterface $userProvider
     * @param CacheItemPoolInterface $cache
     * @param RequestStack $requestStack
     */
    public function __construct(
        LegacyContext $legacyContext,
        TokenStorageInterface $tokenStorage,
        UserProviderInterface $userProvider,
        CacheItemPoolInterface $cache,
        RequestStack $requestStack
    ) {
        $this->legacyContext = $legacyContext;
        $this->tokenStorage = $tokenStorage;
        $this->userProvider = $userProvider;
        $this->cache = $cache;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function renewAuthenticationCredentials(EmployeeForAuthentication $employeeForAuthentication)
    {
        $email = $employeeForAuthentication->getEmail()->getValue();

        $this->updateEmailInCookie($email);
        $this->updatePasswordInCookie($employeeForAuthentication->getHashedPassword());
        $this->updateSecurityToken($email);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticationCredentials(EmployeeForAuthentication $employeeForAuthentication)
    {
        $employee = new Employee($employeeForAuthentication->getEmployeeId()->getValue());

        // Assign logged in employee to the context
        $this->legacyContext->getContext()->employee = $employee;

        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->legacyContext->getContext()->employee->remote_addr = (int) ip2long($request->getClientIp());
        }

        // Save employee data to cookie
        $cookie = $this->legacyContext->getContext()->cookie;
        $cookie->id_employee = $employee->id;
        $cookie->email = $employee->email;
        $cookie->profile = $employee->id_profile;
        $cookie->passwd = $employee->passwd;
        $cookie->remote_addr = $employee->remote_addr;

        if (!$employeeForAuthentication->getStayLoggedIn()) {
            $cookie->last_activity = time();
        }

        $cookie->write();
    }

    /**
     * Update employee password in cookie.
     *
     * @param string $hashedPassword
     */
    private function updatePasswordInCookie($hashedPassword)
    {
        $this->legacyContext->getContext()->cookie->passwd = $hashedPassword;
        $this->legacyContext->getContext()->employee->passwd = $hashedPassword;
        $this->legacyContext->getContext()->cookie->write();
    }

    /**
     * Update employee email in cookie.
     *
     * @param string $email
     */
    private function updateEmailInCookie($email)
    {
        $this->legacyContext->getContext()->cookie->email = $email;
        $this->legacyContext->getContext()->employee->email = $email;
        $this->legacyContext->getContext()->cookie->write();
    }

    /**
     * Update security token in token storage.
     *
     * @param string $email
     */
    private function updateSecurityToken($email)
    {
        $cacheKey = sprintf('app.employees_%s', sha1($email));

        if ($this->cache->hasItem($cacheKey)) {
            $this->cache->deleteItem($cacheKey);
        }

        $user = $this->userProvider->loadUserByUsername($email);
        $token = new UsernamePasswordToken($user, null, 'admin', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }
}
