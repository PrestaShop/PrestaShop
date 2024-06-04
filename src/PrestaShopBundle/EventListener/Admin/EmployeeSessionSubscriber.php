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
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\EmployeeSession;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\EmployeeProvider;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use PrestaShopBundle\Service\Routing\Router;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Event\AuthenticationTokenCreatedEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\TokenDeauthenticatedEvent;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This subscriber watches the various authentication event and saves or removes the persisted
 * Employee sessions accordingly. It is also in charge of maintaining some backward compatibility
 * with the legacy cookie.
 */
class EmployeeSessionSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;

    public function __construct(
        private readonly EmployeeProvider $employeeProvider,
        private readonly EmployeeRepository $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
        private readonly LegacyContext $legacyContext,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly RouterInterface $router,
        private readonly ConfigurationInterface $configuration,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationTokenCreatedEvent::class => 'createEmployeeSession',
            LoginSuccessEvent::class => 'onLoginSuccess',
            // Must be executed after the firewall listener
            KernelEvents::REQUEST => [['onKernelRequest', 7]],
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
        $event->getAuthenticatedToken()->setAttribute(TokenAttributes::EMPLOYEE_SESSION, $employeeSession);
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

        // Save the IP used when the user logged in
        if ((bool) $this->configuration->get('PS_COOKIE_CHECKIP')) {
            $requestIpAddress = $event->getRequest()->getClientIp();
            $event->getAuthenticatedToken()->setAttribute(TokenAttributes::IP_ADDRESS, $requestIpAddress);
        }

        // Update the cookie after successful login
        $this->updateLegacyCookie($event->getRequest(), true);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->security->getUser() instanceof Employee) {
            return;
        }

        $employeeSession = $this->getEmployeeSessionFromToken();
        if (!$employeeSession instanceof EmployeeSession) {
            $this->logger->debug('User is logout because no EmployeeSession was found in token');
            $this->logoutAndStopEvent($event);

            return;
        }

        /** @var Employee $employee */
        $employee = $this->security->getUser();
        // Check that session is still persisted and matches the initial saved token
        if (!$employee->hasSession($employeeSession->getId(), $employeeSession->getToken())) {
            $this->logger->debug(sprintf('Employee lo longer has this session token: %d:%s', $employeeSession->getId(), $employeeSession->getToken()));
            $this->logoutAndStopEvent($event);

            return;
        }

        if ((bool) $this->configuration->get('PS_COOKIE_CHECKIP') && $this->getIpAddressFromToken() !== $event->getRequest()->getClientIp()) {
            $this->logger->debug('Employee IP Address does not match with the expected one');
            $this->logoutAndStopEvent($event);

            return;
        }

        // Update the legacy cookie on each request in case it has been modified, this way we make sure the legacy modules and
        // legacy controllers that rely on it always have up-to-date info
        $this->updateLegacyCookie($event->getRequest());
    }

    public function cleanEmployeeSessions(TokenDeauthenticatedEvent $event): void
    {
        /** @var Employee $employee */
        $employee = $this->employeeProvider->loadUserByIdentifier($event->getOriginalToken()->getUserIdentifier());
        if ($employee instanceof Employee) {
            // If the employee has been forcefully deauthenticated, it is safe to assume that all his related sessions
            // can no longer be considered safe, so they are all removed, thus the employee will be logged out on all
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

    protected function logoutAndStopEvent(RequestEvent $event): void
    {
        // Logout the user
        $this->security->logout(false);

        // Set the redirection so the process stops right away
        $event->setResponse(new RedirectResponse($this->router->generate('admin_login')));

        // Save the target path so the next login will redirect to the url requested at the moment of the logout
        $this->saveTargetPath($event->getRequest()->getSession(), 'main', $event->getRequest()->getUri());

        // Stop the event propagation, nothing more needs to happen except for redirection, and it prevents the event to
        // keep travelling and be caught by other unwanted listeners (like the TokenizedUrlsListener)
        $event->stopPropagation();

        $session = $event->getRequest()->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('warning', $this->translator->trans('You have been logged out for security reasons', [], 'Admin.Login.Feature'));
        }
    }

    protected function getEmployeeSessionFromToken(): ?EmployeeSession
    {
        if (!$this->security->getToken()?->hasAttribute(TokenAttributes::EMPLOYEE_SESSION)) {
            return null;
        }

        return $this->security->getToken()->getAttribute(TokenAttributes::EMPLOYEE_SESSION);
    }

    protected function getIpAddressFromToken(): ?string
    {
        if (!$this->security->getToken()?->hasAttribute(TokenAttributes::IP_ADDRESS)) {
            return null;
        }

        return $this->security->getToken()->getAttribute(TokenAttributes::IP_ADDRESS);
    }

    /**
     * Update legacy cookie for backward compatibility, values are always set but we only
     * write it on login success.
     */
    protected function updateLegacyCookie(Request $request, bool $write = false): void
    {
        $legacyCookie = $this->legacyContext->getContext()->cookie;

        // Mimic AdminLogin login action
        $legacyCookie->remote_addr = (int) ip2long($request->getClientIp());
        $employee = $this->security->getUser();
        if ($employee instanceof Employee) {
            $legacyCookie->id_employee = $employee->getId();
            $legacyCookie->email = $employee->getEmail();
            $legacyCookie->profile = $employee->getProfile()->getId();
            $legacyCookie->passwd = $employee->getPassword();
            $legacyCookie->id_lang = $employee->getDefaultLanguage()->getId();
        }

        // Mimic Cookie::registerSession behaviour
        $employeeSession = $this->getEmployeeSessionFromToken();
        if ($employeeSession instanceof EmployeeSession) {
            $legacyCookie->session_id = $employeeSession->getId();
            $legacyCookie->session_token = $employeeSession->getToken();
        }

        if ($write) {
            $legacyCookie->write();
        }
    }
}
