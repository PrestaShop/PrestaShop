<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;

/**
 * Covers ORM writes without depending on the admin authentication services.
 */
final class EmployeeTrustedDeviceVersionListener
{
    public function preUpdate(Employee $employee, PreUpdateEventArgs $event): void
    {
        /** @var EmployeeRepository $repository */
        $repository = $event->getObjectManager()->getRepository(Employee::class);
        $repository->revokeTrustedDevicesForChanges($employee->getId(), $event->getEntityChangeSet());
    }
}
