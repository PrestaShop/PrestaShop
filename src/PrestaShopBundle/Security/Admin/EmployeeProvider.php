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

use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\Employee as DoctrineEmployee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
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
    /**
     * @deprecated Since v9.0 use Employee::ROLE_EMPLOYEE instead
     */
    public const ROLE_EMPLOYEE = Employee::ROLE_EMPLOYEE;

    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly EmployeeRepository $employeeRepository,
    ) {
    }

    /**
     * Fetch the Employee entity that matches the given username.
     * Cache system doesn't support "@" character, so we rely on a sha1 expression.
     *
     * @param string $identifier
     *
     * @return UserInterface
     *
     * @throws UserNotFoundException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $cacheKey = sha1($identifier);
        $cachedEmployee = $this->cache->getItem("app.employees_{$cacheKey}");

        if ($cachedEmployee->isHit()) {
            return $cachedEmployee->get();
        }

        $doctrineEmployee = $this->loadEmployee($identifier);
        $cachedEmployee->set($doctrineEmployee);
        $this->cache->save($cachedEmployee);

        return $cachedEmployee->get();
    }

    /**
     * Reload an Employee and returns a fresh instance.
     *
     * @param UserInterface $employee
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $employee)
    {
        if (!$employee instanceof DoctrineEmployee) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $employee::class));
        }

        // Always reload the employee regardless of the cache
        $freshEmployee = $this->loadEmployee($employee->getUserIdentifier());

        // Update the cache so that loadUserByIdentifier is updated
        $cacheKey = sha1($employee->getUserIdentifier());
        $cachedEmployee = $this->cache->getItem("app.employees_{$cacheKey}");
        $this->cache->save($cachedEmployee);

        return $freshEmployee;
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
        return $class === DoctrineEmployee::class;
    }

    protected function loadEmployee(string $email): DoctrineEmployee
    {
        /** @var DoctrineEmployee|null $doctrineEmployee */
        $doctrineEmployee = $this->employeeRepository->loadEmployeeByIdentifier($email);
        if (empty($doctrineEmployee)) {
            throw new UserNotFoundException(sprintf('Identifier "%s" does not exist.', $email));
        }

        return $doctrineEmployee;
    }
}
