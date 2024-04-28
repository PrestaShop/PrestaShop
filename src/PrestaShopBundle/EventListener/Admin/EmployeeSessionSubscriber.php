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
use PrestaShopBundle\Service\Routing\Router;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Event\AuthenticationTokenCreatedEvent;
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
    public const EMPLOYEE_SESSION_TOKEN_ATTRIBUTE = '_employee_session';

    public function __construct(
        private readonly EmployeeProvider $employeeProvider,
        private readonly EmployeeRepository $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
        private readonly LegacyContext $legacyContext,
        private readonly CsrfTokenManagerInterface $tokenManager,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationTokenCreatedEvent::class => 'createEmployeeSession',
            LoginSuccessEvent::class => 'onLoginSuccess',
            // Must be executed after the firewall listener
            KernelEvents::REQUEST => [['checkEmployeeSession', 7]],
            LogoutEvent::class => 'onLogout',
            TokenDeauthenticatedEvent::class => 'cleanEmployeeSessions',
        ];
    }

    public function createEmployeeSession(AuthenticationTokenCreatedEvent $event): void
    {
        // Load doctrine employee because the event may contain an unserialized object not recognized by the Entity manager
        $employee = $this->employeeRepository->loadEmployeeByIdentifier($event->getAuthenticatedToken()->getUserIdentifier());

        // Create new employee session
        $employeeSession = new EmployeeSession();
        $employeeSession->setToken(sha1(time() . uniqid()));
        $employee->addSession($employeeSession);
        $this->entityManager->persist($employeeSession);
        $this->entityManager->flush();

        // Set the EmployeeSession as a token attribute so that it is serialized in the session
        $event->getAuthenticatedToken()->setAttribute(self::EMPLOYEE_SESSION_TOKEN_ATTRIBUTE, $employeeSession);
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        // At the end of login success Symfony has a mechanism that removes all CSRF tokens for security, but it means it also remove
        // our CSRF token used for URL token validation, thus the redirect url will be invalid and display a compromised page To avoid
        // this we replace the URL token at the last minute with a fresh token that will be valid for the redirected url
        $eventResponse = $event->getResponse();
        if ($eventResponse instanceof RedirectResponse) {
            $tokenizedUrl = Router::generateTokenizedUrl($eventResponse->getTargetUrl(), $this->tokenManager->refreshToken($event->getAuthenticatedToken()->getUserIdentifier())->getValue());
            $eventResponse->setTargetUrl($tokenizedUrl);
            $event->setResponse($eventResponse);
        }

        // Update legacy cookie for backward compatibility, simply set the values and let the cookie write itself
        $legacyCookie = $this->legacyContext->getContext()->cookie;

        // Mimic AdminLogin login action
        $legacyCookie->remote_addr = (int) ip2long($event->getRequest()->getClientIp());
        $employee = $this->security->getUser();
        if ($employee instanceof Employee) {
            $legacyCookie->id_employee = $employee->getId();
            $legacyCookie->email = $employee->getEmail();
            $legacyCookie->profile = $employee->getProfile()->getId();
            $legacyCookie->passwd = $employee->getPassword();
        }

        // Mimic Cookie::registerSession behaviour
        $employeeSession = $this->getEmployeeSessionFromToken();
        if ($employeeSession instanceof EmployeeSession) {
            $legacyCookie->session_id = $employeeSession->getId();
            $legacyCookie->session_token = $employeeSession->getToken();
        }
    }

    public function checkEmployeeSession(KernelEvent $event): void
    {
        if (!$this->security->getUser() instanceof Employee) {
            return;
        }

        $employeeSession = $this->getEmployeeSessionFromToken();
        if (!$employeeSession instanceof EmployeeSession) {
            $this->logger->debug('User is logout because no EmployeeSession was found in token');
            $this->security->logout(false);

            return;
        }

        /** @var Employee $employee */
        $employee = $this->security->getUser();
        // Check that session is still persisted nad matches the initial saved token
        if (!$employee->hasSession($employeeSession->getId(), $employeeSession->getToken())) {
            $this->logger->debug(sprintf('Employee lo longer has this session token: %d:%s', $employeeSession->getId(), $employeeSession->getToken()));
            $this->security->logout(false);
        }
    }

    public function cleanEmployeeSessions(TokenDeauthenticatedEvent $event): void
    {
        /** @var Employee $employee */
        $employee = $this->employeeProvider->loadUserByIdentifier($event->getOriginalToken()->getUserIdentifier());
        if ($employee instanceof Employee) {
            // If the employee has been forcefully deauthenticated, it is safe to assume that all his related sessions
            // can no longer be considered safe so they are all removed, thus the employee will be logged out on all
            // their devices
            $employee->removeAllSessions();
            $this->entityManager->flush();
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $employeeSession = $this->getEmployeeSessionFromToken();
        if ($employeeSession instanceof EmployeeSession) {
            // Fetch the Doctrine employee to modify the DB content
            /** @var Employee $employee */
            $employee = $this->employeeProvider->loadUserByIdentifier($user->getUserIdentifier());
            $employee->removeSessionById($employeeSession->getId());
            $this->entityManager->flush();
        }

        // Logout cookie for backward compatibility
        $this->legacyContext->getContext()->cookie->logout();
    }

    protected function getEmployeeSessionFromToken(): ?EmployeeSession
    {
        if (!$this->security->getToken()?->hasAttribute(self::EMPLOYEE_SESSION_TOKEN_ATTRIBUTE)) {
            return null;
        }

        return $this->security->getToken()->getAttribute(self::EMPLOYEE_SESSION_TOKEN_ATTRIBUTE);
    }
}
