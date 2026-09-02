<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use ReflectionMethod;
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

        $method = new ReflectionMethod($controllerObject, $methodName);
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
