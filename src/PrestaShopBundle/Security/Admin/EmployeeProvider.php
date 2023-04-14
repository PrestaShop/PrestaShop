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

namespace PrestaShopBundle\Security\Admin;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Security\EmployeePermissionProviderInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class EmployeeProvider To retrieve Employee entities for the Symfony security components.
 */
class EmployeeProvider implements UserProviderInterface
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    private $legacyContext;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var EmployeePermissionProviderInterface
     */
    private $employeePermissionProvider;

    public function __construct(
        LegacyContext $context,
        CacheItemPoolInterface $cache,
        EmployeePermissionProviderInterface $employeePermissionProvider
    ) {
        $this->legacyContext = $context->getContext();
        $this->cache = $cache;
        $this->employeePermissionProvider = $employeePermissionProvider;
    }

    /**
     * Fetch the Employee entity that matches the given username.
     * Cache system doesn't supports "@" character, so we rely on a sha1 expression.
     *
     * @param string $username
     *
     * @return Employee
     *
     * @throws UserNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $cacheKey = sha1($username);
        $cachedEmployee = $this->cache->getItem("app.employees_${cacheKey}");

        if ($cachedEmployee->isHit()) {
            return $cachedEmployee->get();
        }

        if (
            null !== $this->legacyContext->employee
            && $this->legacyContext->employee->email === $username
        ) {
            $employee = new Employee($this->legacyContext->employee);
            $employee->setRoles(
                array_merge([self::ROLE_EMPLOYEE], $this->employeePermissionProvider->getRoles($this->legacyContext->employee->id_profile))
            );

            $cachedEmployee->set($employee);
            $this->cache->save($cachedEmployee);

            return $cachedEmployee->get();
        }

        throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    /**
     * Reload an Employee and returns a fresh instance.
     *
     * @param UserInterface $employee
     *
     * @return Employee
     */
    public function refreshUser(UserInterface $employee)
    {
        if (!$employee instanceof Employee) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($employee)));
        }

        return $this->loadUserByUsername($employee->getUsername());
    }

    /**
     * Tests if the given class supports the security layer. Here, only Employee class is allowed to be used to authenticate.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'PrestaShopBundle\Security\Admin\Employee';
    }
}
