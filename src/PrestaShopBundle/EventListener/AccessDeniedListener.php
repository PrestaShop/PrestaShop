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

use Doctrine\Common\Annotations\Reader;
use PrestaShopBundle\Security\Annotation\AdminSecurity as AdminSecurityAnnotation;
use PrestaShopBundle\Security\Attribute\AdminSecurity as AdminSecurityAttribute;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
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
    use ExternalApiTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
        private readonly Reader $annotationReader,
    ) {
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if (!$event->isMainRequest()
            || !$event->getThrowable() instanceof AccessDeniedException
            || $this->isResourceApiRequest($event->getRequest())
        ) {
            return;
        }

        $controllerName = $event->getRequest()->attributes->get('_controller');

        [$controller, $method] = explode('::', $controllerName, 2);

        if (empty($controller) || !class_exists($controller) || !method_exists($controller, $method)) {
            return;
        }

        $reflectionMethod = new ReflectionMethod($controller, $method);

        $attributes = $reflectionMethod->getAttributes(AdminSecurityAttribute::class);

        if (!empty($attributes)) {
            $this->handleAttributes($attributes, $event);

            return;
        }

        $reflectionClass = new ReflectionClass($controller);

        $attributes = $reflectionClass->getAttributes(AdminSecurityAttribute::class);

        if (!empty($attributes)) {
            $this->handleAttributes($attributes, $event);

            return;
        }

        // annotation management
        $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, AdminSecurityAnnotation::class);

        if ($annotation != null) {
            $event->allowCustomResponseCode();

            $event->setResponse(
                $this->getAccessDeniedResponse($event->getRequest(), $annotation)
            );
        }
    }

    /**
     * @return Response
     */
    private function getAccessDeniedResponse(Request $request, AdminSecurityAttribute $adminSecurity)
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

    // Compute the url for the redirection.
    private function computeRedirectionUrl(AdminSecurityAttribute $adminSecurity, Request $request): string
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

    // Gets query parameters by comparing them to the current request attributes.
    private function getQueryParamsFromRequestQuery(array $queryParametersToKeep, Request $request): array
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

    private function getErrorMessage(AdminSecurityAttribute $adminSecurity): string
    {
        return $this->translator->trans(
            $adminSecurity->getMessage(),
            [],
            $adminSecurity->getDomain()
        );
    }

    public function handleAttributes(array $attributes, ExceptionEvent $event): void
    {
        foreach ($attributes as $attribute) {
            /** @var AdminSecurityAttribute $adminSecurity */
            $adminSecurity = $attribute->newInstance();
            if (null != $adminSecurity->getRedirectRoute()) {
                $event->allowCustomResponseCode();

                $event->setResponse(
                    $this->getAccessDeniedResponse($event->getRequest(), $adminSecurity)
                );
                break;
            }
        }
    }
}
