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

use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listener dedicated to set up Shop context for the Back-Office/Admin application.
 */
class ShopContextListener
{
    public function __construct(
        private readonly ShopContextBuilder $shopContextBuilder,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopConfigurationInterface $configuration,
        private readonly LegacyContext $legacyContext,
        private readonly MultistoreFeature $multistoreFeature,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @throws ReflectionException
     * @throws ShopException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $psSslEnabled = (bool) $this->configuration->get('PS_SSL_ENABLED', null, ShopConstraint::allShops());

        $this->shopContextBuilder->setSecureMode($psSslEnabled && $event->getRequest()->isSecure());

        $redirectResponse = $this->redirectShopContext($event);
        if ($redirectResponse) {
            $event->setResponse($redirectResponse);

            return;
        }

        if (!$this->multistoreFeature->isUsed()) {
            $shopConstraint = ShopConstraint::shop($this->getConfiguredDefaultShopId());
        } else {
            $shopConstraint = $this->getMultiShopConstraint($event->getRequest());
        }
        $this->shopContextBuilder->setShopConstraint($shopConstraint);

        // In all cases a shop must be set for the context even if it's the default one
        if (!$shopConstraint->getShopId()) {
            $this->shopContextBuilder->setShopId($this->getConfiguredDefaultShopId());
        } else {
            $this->shopContextBuilder->setShopId($shopConstraint->getShopId()->getValue());
        }

        // Set shop constraint easily accessible via request attribute
        $event->getRequest()->attributes->set('shopConstraint', $shopConstraint);
    }

    private function getConfiguredDefaultShopId(): int
    {
        return (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops());
    }

    /**
     * @throws ShopException
     * @throws ReflectionException
     */
    private function getMultiShopConstraint(Request $request): ShopConstraint
    {
        $shopConstraint = $this->verifyRouteAttribute($request);

        if ($shopConstraint) {
            return $shopConstraint;
        }

        $shopConstraint = ShopConstraint::allShops();
        $cookieShopConstraint = $this->getShopConstraintFromCookie();
        if ($cookieShopConstraint) {
            if ($cookieShopConstraint->getShopGroupId()) {
                // Check if the employee has permission on selected group if not fallback on single shop context with employee's default shop
                if ($this->employeeContext->hasAuthorizationOnShopGroup($cookieShopConstraint->getShopGroupId()->getValue())) {
                    $shopConstraint = $cookieShopConstraint;
                } elseif (!empty($this->employeeContext->getDefaultShopId())) {
                    $shopConstraint = ShopConstraint::shop($this->employeeContext->getDefaultShopId());
                }
            } elseif ($cookieShopConstraint->getShopId()) {
                // Check if employee has authorization on selected shop if not fallback on single shop context with employee's default shop
                if ($this->employeeContext->hasAuthorizationOnShop($cookieShopConstraint->getShopId()->getValue())) {
                    $shopConstraint = $cookieShopConstraint;
                } elseif (!empty($this->employeeContext->getDefaultShopId())) {
                    $shopConstraint = ShopConstraint::shop($this->employeeContext->getDefaultShopId());
                } else {
                    $shopConstraint = ShopConstraint::shop($this->getConfiguredDefaultShopId());
                }
            }
        }

        return $shopConstraint;
    }

    /**
     * Get shop context from the legacy cookie, the value of Cookie::shopContext looks like this:
     *  s-1 -> Single shop with shop ID 1
     *  g-2 -> Shop group with shop group ID 2
     *  empty/other values: All Shops
     *
     * @return ShopConstraint|null
     */
    private function getShopConstraintFromCookie(): ?ShopConstraint
    {
        $shopContext = $this->legacyContext->getContext()->cookie->shopContext;
        if (empty($shopContext)) {
            return null;
        }

        $splitShopContext = explode('-', $shopContext);
        if (count($splitShopContext) == 2) {
            $splitShopType = $splitShopContext[0];
            $splitShopValue = (int) $splitShopContext[1];
            if (empty($splitShopValue)) {
                return null;
            }

            if ($splitShopType == 'g') {
                return ShopConstraint::shopGroup($splitShopValue);
            } else {
                return ShopConstraint::shop($splitShopValue);
            }
        }

        return null;
    }

    /**
     * Update cookie value and redirect to current url to refresh the context.
     *
     * @param RequestEvent $requestEvent
     */
    private function redirectShopContext(RequestEvent $requestEvent): ?RedirectResponse
    {
        if (!$this->multistoreFeature->isUsed()) {
            return null;
        }

        $shopContextUrlParameter = $requestEvent->getRequest()->get('setShopContext', null);
        if (null === $shopContextUrlParameter) {
            return null;
        }

        $cookie = $this->legacyContext->getContext()->cookie;
        if ($cookie->shopContext === $shopContextUrlParameter) {
            return null;
        }

        // Apply new shop context by saving it into the cookie and refreshing the current page
        $cookie->shopContext = $shopContextUrlParameter;
        $cookie->write();

        // Redirect to same url but remove setShopContext and conf parameters
        return new RedirectResponse(UrlCleaner::cleanUrl(
            $requestEvent->getRequest()->getUri(),
            ['setShopContext', 'conf']
        ));
    }

    /**
     * @throws ReflectionException
     */
    private function verifyRouteAttribute(Request $request): ?ShopConstraint
    {
        try {
            $routeInfo = $this->router->match($request->getPathInfo());
            $controller = $routeInfo['_controller'];
            [$className, $methodName] = explode('::', $controller);

            $reflectionClass = new ReflectionClass($className);
            $classAttributes = $reflectionClass->getAttributes(AllShopContext::class);
            $methodAttributes = $reflectionClass->getMethod($methodName)->getAttributes(AllShopContext::class);

            $attributes = array_merge($classAttributes, $methodAttributes);
            if (!empty($attributes)) {
                return ShopConstraint::allShops();
            } else {
                return null;
            }
        } catch (NoConfigurationException) {
            return null;
        }
    }
}
