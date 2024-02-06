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

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(
    event: ControllerEvent::class,
    method: 'onKernelController',
)]
class DemoModeEnabledListener
{
    public function __construct(
        private readonly ShopConfigurationInterface $shopConfiguration,
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$this->shopConfiguration->getBoolean('_PS_MODE_DEMO_') || !$event->isMainRequest()) {
            return;
        }

        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        [$controllerObject, $methodName] = $controller;

        $method = new \ReflectionMethod($controllerObject, $methodName);
        $attributes = $method->getAttributes(DemoRestricted::class);

        if ([] === $attributes) {
            return;
        }

        $attribute = $attributes[0];

        /** @var DemoRestricted $demoRestricted */
        $demoRestricted = $attribute->newInstance();

        $this->showNotificationMessage($demoRestricted);

        $routeParametersToKeep = $this->getQueryParamsFromRequestQuery(
            $demoRestricted->getRedirectQueryParamsToKeep(),
            $event->getRequest()
        );

        $url = $this->router->generate($demoRestricted->getRedirectRoute(), $routeParametersToKeep);

        $event->setController(function () use ($url) {
            return new RedirectResponse($url);
        });
    }

    /**
     * Send an error message when redirected, will only work on migrated pages.
     *
     * @param DemoRestricted $demoRestricted
     */
    private function showNotificationMessage(DemoRestricted $demoRestricted)
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add(
            'error',
            $this->translator->trans(
                $demoRestricted->getMessage(),
                [],
                $demoRestricted->getDomain()
            )
        );
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
}
