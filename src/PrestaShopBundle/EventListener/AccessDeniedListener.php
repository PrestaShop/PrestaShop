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

namespace PrestaShopBundle\EventListener;

use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Allow a redirection to the right url when using BetterSecurity annotation.
 */
class AccessDeniedListener
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if (!$event->isMainRequest()
            || !$event->getThrowable() instanceof AccessDeniedException
            || !$securityConfigurations = $event->getRequest()->attributes->get('_security')
        ) {
            return;
        }

        foreach ($securityConfigurations as $securityConfiguration) {
            if ($securityConfiguration instanceof AdminSecurity) {
                $event->allowCustomResponseCode();

                $event->setResponse(
                    $this->getAccessDeniedResponse($event->getRequest(), $securityConfiguration)
                );

                return;
            }
        }
    }

    /**
     * @param Request $request
     * @param AdminSecurity $adminSecurity
     *
     * @return Response
     */
    private function getAccessDeniedResponse(Request $request, AdminSecurity $adminSecurity)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => false,
                'message' => $this->getErrorMessage($adminSecurity),
            ], Response::HTTP_FORBIDDEN);
        }
        /** @var Session $session */
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('error', $this->getErrorMessage($adminSecurity));

        return new RedirectResponse(
            $this->computeRedirectionUrl($adminSecurity, $request)
        );
    }

    /**
     * Compute the url for the redirection.
     *
     * @param AdminSecurity $adminSecurity
     * @param Request $request
     *
     * @return string
     */
    private function computeRedirectionUrl(AdminSecurity $adminSecurity, Request $request)
    {
        $route = $adminSecurity->getRedirectRoute();

        if ($route !== null) {
            $redirectQueryParameters = $adminSecurity->getRedirectQueryParamsToKeep();
            $routeParamsToKeep = $this->getQueryParamsFromRequestQuery(
                $redirectQueryParameters,
                $request
            );

            return $this->router->generate($route, $routeParamsToKeep);
        }

        return $adminSecurity->getUrl();
    }

    /**
     * Gets query parameters by comparing them to the current request attributes.
     *
     * @param array $queryParametersToKeep
     * @param Request $request
     *
     * @return array
     */
    private function getQueryParamsFromRequestQuery(array $queryParametersToKeep, Request $request)
    {
        $result = [];

        foreach ($queryParametersToKeep as $queryParameterName) {
            $value = $request->get($queryParameterName);
            if (null !== $value) {
                $result[$queryParameterName] = $value;
            }
        }

        return $result;
    }

    /**
     * @param AdminSecurity $adminSecurity
     *
     * @return string
     */
    private function getErrorMessage(AdminSecurity $adminSecurity)
    {
        return $this->translator->trans(
            $adminSecurity->getMessage(),
            [],
            $adminSecurity->getDomain()
        );
    }
}
