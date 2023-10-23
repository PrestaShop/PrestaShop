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

namespace PrestaShopBundle\EventListener\Context\Admin;

use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/36
 */
class ShopContextListener
{
    public function __construct(
        private readonly ShopContextBuilder $shopContextBuilder,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopConfigurationInterface $configuration,
        private readonly LegacyContext $legacyContext,
        private readonly MultistoreFeature $multistoreFeature,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Once container will be both in FO and BO we'll need to check here that we're in a BO context
        // either that or the listener itself should be configured in a way so that it only is used in BO context
        // because in FO we don't handle shop context the same way (there can be only one shop and no shop context
        // switching is possible)
        if (!$event->isMainRequest()) {
            return;
        }

        $shopConstraint = ShopConstraint::allShops();
        $cookieShopConstraint = $this->getShopConstraintFromCookie();

        if (!empty($event->getRequest()->get('setShopContext'))) {
            $this->changeShopContext($event->getRequest()->get('setShopContext'));
        }

        if ($cookieShopConstraint && $cookieShopConstraint->getShopGroupId()) {
            // Check if the employee has permission on selected group if not fallback on single shop context with employee's default shop
            if (empty($this->employeeContext->getEmployee()) || $this->employeeContext->hasAuthorizationOnShopGroup($cookieShopConstraint->getShopGroupId()->getValue())) {
                $shopConstraint = $cookieShopConstraint;
            } elseif (!empty($this->employeeContext->getDefaultShopId())) {
                $shopConstraint = ShopConstraint::shop($this->employeeContext->getDefaultShopId());
            }
        } elseif ($cookieShopConstraint && $cookieShopConstraint->getShopId()) {
            // Check if employee has authorization on selected shop if not fallback on single shop context with employee's default shop
            if (empty($this->employeeContext->getEmployee()) || $this->employeeContext->hasAuthorizationOnShop($cookieShopConstraint->getShopId()->getValue())) {
                $shopConstraint = $cookieShopConstraint;
            } elseif (!empty($this->employeeContext->getDefaultShopId())) {
                $shopConstraint = ShopConstraint::shop($this->employeeContext->getDefaultShopId());
            }
        }

        if (empty($this->shopContextBuilder->getShopConstraint())) {
            $this->shopContextBuilder->setShopConstraint($shopConstraint);
        }

        // In all cases a shop must be set for the context even if it's the default one
        if (empty($this->shopContextBuilder->getShopId()) && !$shopConstraint->getShopId()) {
            $this->shopContextBuilder->setShopId((int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops()));
        } elseif (empty($this->shopContextBuilder->getShopId())) {
            $this->shopContextBuilder->setShopId($shopConstraint->getShopId()->getValue());
        }

        $this->shopContextBuilder->buildLegacyContext($shopConstraint);
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
        if (empty($this->legacyContext->getContext()->cookie->shopContext)) {
            return null;
        }

        $splitShopContext = explode('-', $this->legacyContext->getContext()->cookie->shopContext);
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

    private function changeShopContext(string $shopContextUrlParameter)
    {
        if ($this->multistoreFeature->isUsed()) {
            $this->legacyContext->getContext()->cookie->shopContext = $shopContextUrlParameter;
        } else {
            $this->legacyContext->getContext()->cookie->shopContext = $this->configuration->get('PS_SHOP_DEFAULT');
        }
    }
}
