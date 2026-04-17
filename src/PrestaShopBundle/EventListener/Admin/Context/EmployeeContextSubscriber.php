<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Security\Admin\SessionEmployeeProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Listener dedicated to set up Employee context for the Back-Office/Admin application.
 */
class EmployeeContextSubscriber implements EventSubscriberInterface
{
    /**
     * Priority a bit lower than the FirewallListener
     */
    public const KERNEL_REQUEST_PRIORITY = 7;

    public function __construct(
        private readonly EmployeeContextBuilder $employeeContextBuilder,
        private readonly Security $security,
        private readonly SessionEmployeeProvider $sessionEmployeeProvider,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', self::KERNEL_REQUEST_PRIORITY],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $employeeId = null;
        // First see if an employee is logged in
        if ($this->security->getUser() instanceof Employee) {
            $employeeId = $this->security->getUser()->getId();
        }
        // Then fetch the employee ID from the session
        if (empty($employeeId)) {
            $employeeId = $this->sessionEmployeeProvider->getEmployeeFromSession($event->getRequest())?->getId();
        }

        if (!empty($employeeId)) {
            $this->employeeContextBuilder->setEmployeeId($employeeId);
        }
    }
}
