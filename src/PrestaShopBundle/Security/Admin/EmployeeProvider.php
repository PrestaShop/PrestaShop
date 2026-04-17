<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
