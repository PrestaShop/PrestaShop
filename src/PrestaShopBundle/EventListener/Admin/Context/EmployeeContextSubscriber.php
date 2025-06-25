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
