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

namespace PrestaShopBundle\EventListener\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\EmployeeSession;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\EmployeeProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\TokenDeauthenticatedEvent;

/**
 * This subscriber watches the various authentication event and saves or removes the persisted
 * Employee sessions accordingly. It is also in charge of maintaining some backward compatibility
 * with the legacy cookie.
 */
class EmployeeSessionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LegacyContext $legacyContext,
        private readonly EmployeeProvider $employeeProvider,
        private readonly EmployeeRepository $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout',
            TokenDeauthenticatedEvent::class => 'onTokenDeauthenticated',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        // Load doctrine employee because the even maybe contain an unserialized object not recognized by the Entity manager
        $employee = $this->employeeRepository->loadEmployeeByIdentifier($event->getUser()->getUserIdentifier());

        // Create new employee session
        $employeeSession = new EmployeeSession();
        $employeeSession->setToken(sha1(time() . uniqid()));
        // $employee->addSession($employeeSession);
        $employeeSession->setEmployee($employee);
        $this->entityManager->persist($employeeSession);
        $this->entityManager->flush();

        // Update legacy cookie

        // Mimic legacy login controller behaviour
        $legacyCookie = $this->legacyContext->getContext()->cookie;
        $legacyCookie->email = $employee->getEmail();
        $legacyCookie->profile = $employee->getProfile()->getId();
        $legacyCookie->passwd = $employee->getPassword();
        $legacyCookie->remote_addr = (int) ip2long($event->getRequest()->getClientIp());
        // Mimic Cookie::registerSession behaviour
        $legacyCookie->id_employee = $employee->getId();
        $legacyCookie->session_id = $employeeSession->getId();
        $legacyCookie->session_token = $employeeSession->getToken();

        $legacyCookie->write();

        /** @var Employee $loggedInEmployee */
        $loggedInEmployee = $event->getUser();
        // We set the session token and session ID on the event user because it will then be serialized in the session
        // thus allowing us to check if the session is still alive in future requests.
        $loggedInEmployee
            ->setSessionId($employeeSession->getId())
            ->setSessionToken($employeeSession->getToken())
        ;
    }

    public function onTokenDeauthenticated(TokenDeauthenticatedEvent $event): void
    {
        /** @var Employee $employee */
        $employee = $this->employeeProvider->loadUserByIdentifier($event->getOriginalToken()->getUserIdentifier());
        if ($employee instanceof Employee) {
            // If the employee has been forcefully deauthenticated it may be because someone is trying to still their
            // session so for safety all current sessions are removed, it means the employee is logged out on all the
            // devices he as logged on
            $employee->removeAllSessions();
            $this->entityManager->flush();
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()->getUser();
        if ($user instanceof Employee && !empty($user->getSessionId())) {
            // Fetch the Doctrine employee to modify the DB content
            /** @var Employee $employee */
            $employee = $this->employeeProvider->loadUserByIdentifier($user->getUserIdentifier());
            $employee->removeSessionById($user->getSessionId());
            $this->entityManager->flush();
        }

        // Logout cookie for backward compatibility
        $this->legacyContext->getContext()->cookie->logout();
    }
}
