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
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Routing\LegacyControllerConstants;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listener dedicated to set up Shop context for the Back-Office/Admin application.
 */
class ShopContextListener implements EventSubscriberInterface
{
    /**
     * Priority lower than EmployeeContextListener so that EmployeeContext is correctly initialized
     */
    public const KERNEL_REQUEST_PRIORITY = EmployeeContextListener::KERNEL_REQUEST_PRIORITY - 1;

    /**
     * Priority higher than Symfony router listener (which is 32)
     */
    public const BEFORE_ROUTER_PRIORITY = 33;

    public function __construct(
        private readonly ShopContextBuilder $shopContextBuilder,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopConfigurationInterface $configuration,
        private readonly MultistoreFeature $multistoreFeature,
        private readonly RouterInterface $router,
        private readonly Security $security,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['initDefaultShopContext', self::BEFORE_ROUTER_PRIORITY],
                ['initShopContext', self::KERNEL_REQUEST_PRIORITY],
            ],
        ];
    }

    public function initDefaultShopContext(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $shopConstraint = ShopConstraint::shop((int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops()));
        $this->shopContextBuilder->setShopId($shopConstraint->getShopId()->getValue());
        $this->shopContextBuilder->setShopConstraint($shopConstraint);
    }

    /**
     * @throws ReflectionException
     * @throws ShopException
     */
    public function initShopContext(RequestEvent $event): void
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
        $shopConstraint = $this->getShopConstraintFromRouteAttribute($request);
        if ($shopConstraint) {
            return $shopConstraint;
        }

        // Check if the displayed legacy controller forces All shops mode (check already performed by LegacyRouterChecker)
        $isAllShopContext = $request->attributes->get(LegacyControllerConstants::IS_ALL_SHOP_CONTEXT_ATTRIBUTE);

        if ($isAllShopContext) {
            return ShopConstraint::allShops();
        }

        $shopConstraint = ShopConstraint::allShops();
        $cookieShopConstraint = $this->getShopConstraintFromTokenAttribute();
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
     * Get shop context from the token attribute.
     *
     * @return ShopConstraint|null
     */
    private function getShopConstraintFromTokenAttribute(): ?ShopConstraint
    {
        if (!$this->security->getToken() || !$this->security->getToken()->hasAttribute(TokenAttributes::SHOP_CONSTRAINT)) {
            return null;
        }

        $shopConstraint = $this->security->getToken()->getAttribute(TokenAttributes::SHOP_CONSTRAINT);
        if ($shopConstraint instanceof ShopConstraint) {
            return $shopConstraint;
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

        $parameterShopConstraint = $this->getShopConstraintFromParameter($shopContextUrlParameter);
        $tokenShopConstraint = $this->getShopConstraintFromTokenAttribute();

        // If the requested shop constraint is the current one nothing to change
        if (null !== $tokenShopConstraint && $parameterShopConstraint->isEqual($tokenShopConstraint)) {
            return null;
        }

        // Update the token attribute value, it will be persisted by Symfony at the end of the redirect request
        $this->security->getToken()->setAttribute(TokenAttributes::SHOP_CONSTRAINT, $parameterShopConstraint);

        // Redirect to same url but remove setShopContext and conf parameters
        return new RedirectResponse(UrlCleaner::cleanUrl(
            $requestEvent->getRequest()->getUri(),
            ['setShopContext', 'conf']
        ));
    }

    /**
     * The parameter value looks like this:
     *   s-1 -> Single shop with shop ID 1
     *   g-2 -> Shop group with shop group ID 2
     *   empty/other values: All Shops
     */
    private function getShopConstraintFromParameter(string $parameter): ShopConstraint
    {
        if (empty($parameter)) {
            return ShopConstraint::allShops();
        }

        $splitShopContext = explode('-', $parameter);
        if (count($splitShopContext) == 2) {
            $splitShopType = $splitShopContext[0];
            $splitShopValue = (int) $splitShopContext[1];
            if (!empty($splitShopValue) && !empty($splitShopType)) {
                if ($splitShopType == 'g') {
                    return ShopConstraint::shopGroup($splitShopValue);
                } elseif ($splitShopType == 's') {
                    return ShopConstraint::shop($splitShopValue);
                }
            }
        }

        return ShopConstraint::allShops();
    }

    private function getShopConstraintFromRouteAttribute(Request $request): ?ShopConstraint
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
        } catch (NoConfigurationException|ReflectionException) {
            return null;
        }
    }
}
