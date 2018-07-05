<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Allow a redirection to the right url when using BetterSecurity annotation.
 */
class AccessDeniedListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(RouterInterface $router, TranslatorInterface $translator, SessionInterface $session)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->session = $session;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()
            || !$event->getException() instanceof AccessDeniedException
            || !$securityConfigurations = $event->getRequest()->attributes->get('_security')
        ) {
            return;
        }

        foreach ($securityConfigurations as $securityConfiguration) {
            if ($securityConfiguration instanceof AdminSecurity) {
                $event->allowCustomResponseCode();

                $this->showNotificationMessage($securityConfiguration);
                $url = $this->computeRedirectionUrl($securityConfiguration);

                $event->setResponse(new RedirectResponse($url));

                return;
            }
        }
    }

    /**
     * Compute the url for the redirection.
     *
     * @param AdminSecurity $adminSecurity
     *
     * @return string
     */
    private function computeRedirectionUrl(AdminSecurity $adminSecurity)
    {
        $route = $adminSecurity->getRedirectRoute();
        if (null !== $route) {
            return $this->router->generate($route);
        }

        return $adminSecurity->getUrl();
    }

    /**
     * Send an error message when redirected, will only work on migrated pages.
     *
     * @param AdminSecurity $adminSecurity
     */
    private function showNotificationMessage(AdminSecurity $adminSecurity)
    {
        $this->session->getFlashBag()->add(
            'error',
            $this->translator->trans(
                $adminSecurity->getMessage(),
                [],
                $adminSecurity->getDomain()
            )
        );
    }
}
