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

    /**
     * @var array<string, Employee>
     */
    private array $employees = [];

    public function __construct(
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
        if (isset($this->employees[$identifier])) {
            return $this->employees[$identifier];
        }

        $this->employees[$identifier] = $this->loadEmployee($identifier, false);

        return $this->employees[$identifier];
    }

    /**
     * Reload an Employee based on the serialized one and returns a fresh instance.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof DoctrineEmployee) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        // Always reload the employee regardless of the cache
        $freshEmployee = $this->loadEmployee($user->getUserIdentifier(), true);
        // Update the cache so that loadUserByIdentifier is updated
        $this->employees[$user->getUserIdentifier()] = $freshEmployee;

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

    protected function loadEmployee(string $email, bool $refresh): DoctrineEmployee
    {
        /** @var DoctrineEmployee|null $doctrineEmployee */
        $doctrineEmployee = $this->employeeRepository->loadEmployeeByIdentifier($email, $refresh);
        if (empty($doctrineEmployee) || !$doctrineEmployee->isActive()) {
            throw new UserNotFoundException(sprintf('Identifier "%s" does not exist.', $email));
        }

        return $doctrineEmployee;
    }
}
