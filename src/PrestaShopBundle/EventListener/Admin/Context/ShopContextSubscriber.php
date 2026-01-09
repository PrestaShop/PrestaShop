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
use PrestaShopBundle\Routing\LegacyControllerConstants;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Listener dedicated to set up Shop context for the Back-Office/Admin application.
 */
class ShopContextSubscriber implements EventSubscriberInterface
{
    /**
     * Priority lower than EmployeeContextListener so that EmployeeContext is correctly initialized
     */
    public const KERNEL_REQUEST_PRIORITY = EmployeeContextSubscriber::KERNEL_REQUEST_PRIORITY - 1;

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
        private readonly LegacyContext $legacyContext,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['initDefaultShopContext', self::BEFORE_ROUTER_PRIORITY],
                ['initShopContext', self::KERNEL_REQUEST_PRIORITY],
            ],
            AuthenticationSuccessEvent::class => 'initShopContextOnLogin',
        ];
    }

    public function initShopContextOnLogin(AuthenticationSuccessEvent $authenticationSuccessEvent): void
    {
        // Set the initial shop constraint right after the employee successfully logged in
        if ($this->employeeContext->hasAuthorizationForAllShops()) {
            $authenticationSuccessEvent->getAuthenticationToken()->setAttribute(TokenAttributes::SHOP_CONSTRAINT, ShopConstraint::allShops());
            $this->legacyContext->getContext()->cookie->shopContext = '';
        } elseif ($this->employeeContext->getDefaultShopId()) {
            $authenticationSuccessEvent->getAuthenticationToken()->setAttribute(TokenAttributes::SHOP_CONSTRAINT, ShopConstraint::shop($this->employeeContext->getDefaultShopId()));
            $this->legacyContext->getContext()->cookie->shopContext = 's-' . $this->employeeContext->getDefaultShopId();
        }
    }

    public function initDefaultShopContext(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $defaultShopId = $this->getConfiguredDefaultShopId();
        $this->shopContextBuilder->setShopId($defaultShopId);
        $this->shopContextBuilder->setShopConstraint(ShopConstraint::shop($defaultShopId));
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

        $request = $event->getRequest();
        $psSslEnabled = (bool) $this->configuration->get('PS_SSL_ENABLED', null, ShopConstraint::allShops());
        $this->shopContextBuilder->setSecureMode($psSslEnabled && $request->isSecure());

        $redirectResponse = $this->redirectShopContext($event);
        if ($redirectResponse) {
            $event->setResponse($redirectResponse);

            return;
        }

        $shopConstraint = $this->determineShopConstraint($request);

        // If not shop constraint was definable it means the employee may not be logged in In any case we have no info to setup the
        // shop context more accurately so we do nothing
        if (!$shopConstraint) {
            return;
        }

        // If the employee is currently on a shop context he is not allowed to, they are redirected to their default shop
        if ($this->employeeContext->getDefaultShopId() && !$this->isAuthorizedByShopConstraint($shopConstraint)) {
            $this->addFlashErrorMessage($shopConstraint, $event);
            if ($event->getRequest()->hasSession() && $event->getRequest()->getSession() instanceof FlashBagAwareSessionInterface) {
                $event->getRequest()->getSession()->getFlashBag()->add('info', $this->translator->trans(
                    'You have been automatically switched to your default store.',
                    [],
                    'Admin.Notifications.Info',
                ));
            }

            $redirectResponse = $this->createShopContextUpdateRedirectResponse(ShopConstraint::shop($this->employeeContext->getDefaultShopId()), $event);
            $event->setResponse($redirectResponse);

            return;
        }

        // Now we are sure the shop constraint is valid and we can initialize the ShopContext
        $this->shopContextBuilder->setShopConstraint($shopConstraint);

        // Always set a shop ID for the context
        $shopId = $shopConstraint->getShopId() ? $shopConstraint->getShopId()->getValue() : $this->getConfiguredDefaultShopId();
        $this->shopContextBuilder->setShopId($shopId);

        // Set shop constraint as request attribute
        $request->attributes->set('shopConstraint', $shopConstraint);
    }

    private function getConfiguredDefaultShopId(): int
    {
        return (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops());
    }

    private function determineShopConstraint(Request $request): ?ShopConstraint
    {
        // Firstly check if the displayed legacy controller forces All shops mode
        $legacyConstraint = $this->getLegacyMultiShopConstraint($request);
        if ($legacyConstraint) {
            return $legacyConstraint;
        }

        if ($this->multistoreFeature->isUsed()) {
            return $this->getMultiShopConstraint($request);
        }

        return ShopConstraint::shop($this->getConfiguredDefaultShopId());
    }

    /**
     * @return ShopConstraint|null
     */
    private function getLegacyMultiShopConstraint(Request $request)
    {
        $multishopContext = $request->attributes->get(LegacyControllerConstants::MULTISHOP_CONTEXT_ATTRIBUTE);

        if ($multishopContext === ShopConstraint::ALL_SHOPS) {
            return ShopConstraint::allShops();
        }

        return null;
    }

    /**
     * @throws ShopException
     * @throws ReflectionException
     */
    private function getMultiShopConstraint(Request $request): ?ShopConstraint
    {
        $shopConstraint = $this->getShopConstraintFromRouteAttribute($request);
        if ($shopConstraint) {
            return $shopConstraint;
        }

        return $this->getShopConstraintFromTokenAttribute();
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
     * Update token attribute value and redirect to current url to refresh the context.
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

        // If the requested shop constraint is the current one nothing to change
        $tokenShopConstraint = $this->getShopConstraintFromTokenAttribute();
        if (null !== $tokenShopConstraint && $parameterShopConstraint->isEqual($tokenShopConstraint)) {
            return null;
        }

        if (!$this->isAuthorizedByShopConstraint($parameterShopConstraint)) {
            $this->addFlashErrorMessage($parameterShopConstraint, $requestEvent);
            $updatedShopConstraint = $tokenShopConstraint;
        } else {
            $updatedShopConstraint = $parameterShopConstraint;
        }

        return $this->createShopContextUpdateRedirectResponse($updatedShopConstraint, $requestEvent);
    }

    private function addFlashErrorMessage(ShopConstraint $invalidShopConstraint, RequestEvent $requestEvent): void
    {
        if ($requestEvent->getRequest()->hasSession() && $requestEvent->getRequest()->getSession() instanceof FlashBagAwareSessionInterface) {
            if ($invalidShopConstraint->forAllShops()) {
                $errorMessage = $this->translator->trans(
                    'Authorization not allowed for all stores.',
                    [],
                    'Admin.Notifications.Error',
                );
            } elseif ($invalidShopConstraint->getShopId()) {
                $errorMessage = $this->translator->trans(
                    'Authorization not allowed for this store.',
                    [],
                    'Admin.Notifications.Error',
                );
            } elseif ($invalidShopConstraint->getShopGroupId()) {
                $errorMessage = $this->translator->trans(
                    'Authorization not allowed for this group of stores.',
                    [],
                    'Admin.Notifications.Error',
                );
            } else {
                $errorMessage = $this->translator->trans(
                    'Authorization not allowed for this store context.',
                    [],
                    'Admin.Notifications.Error',
                );
            }

            $requestEvent->getRequest()->getSession()->getFlashBag()->add(
                'error',
                $errorMessage,
            );
        }
    }

    private function createShopContextUpdateRedirectResponse(ShopConstraint $updatedShopConstraint, RequestEvent $requestEvent): RedirectResponse
    {
        // Update the token attribute value, it will be persisted by Symfony at the end of the redirect request
        $this->security->getToken()->setAttribute(TokenAttributes::SHOP_CONSTRAINT, $updatedShopConstraint);

        // Update legacy cookie shop context to make sure it is synced with the token attribute
        $legacyCookie = $this->legacyContext->getContext()->cookie;
        if ($updatedShopConstraint->getShopId()) {
            $legacyCookie->shopContext = 's-' . $updatedShopConstraint->getShopId()->getValue();
        } elseif ($updatedShopConstraint->getShopGroupId()) {
            $legacyCookie->shopContext = 'g-' . $updatedShopConstraint->getShopGroupId()->getValue();
        } else {
            $legacyCookie->shopContext = '';
        }
        $legacyCookie->write();

        // Rebuild the current URI from the Request components to avoid relying on /index.php in the raw URL
        $request = $requestEvent->getRequest();

        $uri = $request->getSchemeAndHttpHost() . $request->getBasePath() . $request->getPathInfo();
        $queryString = $request->getQueryString();
        if ($queryString) {
            $uri .= '?' . $queryString;
        }

        // Redirect to same url but remove setShopContext and conf parameters
        return new RedirectResponse(UrlCleaner::cleanUrl(
            $uri,
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

    private function isAuthorizedByShopConstraint(ShopConstraint $shopConstraint): bool
    {
        if ($shopConstraint->getShopGroupId()) {
            return $this->employeeContext->hasAuthorizationOnShopGroup($shopConstraint->getShopGroupId()->getValue());
        }

        if ($shopConstraint->getShopId()) {
            return $this->employeeContext->hasAuthorizationOnShop($shopConstraint->getShopId()->getValue());
        }

        if ($shopConstraint->forAllShops()) {
            return $this->employeeContext->hasAuthorizationForAllShops();
        }

        return false;
    }
}
