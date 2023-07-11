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

use PrestaShop\PrestaShop\Core\Context\CookieContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ShopContextListener
{
    public function __construct(
        private readonly ShopContextBuilder $shopContextBuilder,
        private readonly CookieContext $cookieContext,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopConfigurationInterface $configuration
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

        $shopContext = Shop::CONTEXT_ALL;
        $shopId = null;
        $shopGroupId = null;

        if ($this->cookieContext->getShopContext() === Shop::CONTEXT_GROUP) {
            if ($this->employeeContext->hasAuthorizationOnShopGroup($this->cookieContext->getShopGroupId())) {
                $shopContext = Shop::CONTEXT_GROUP;
                $shopGroupId = $this->cookieContext->getShopGroupId();
            } else {
                $shopContext = Shop::CONTEXT_SHOP;
                $shopId = $this->employeeContext->getDefaultShopId();
            }
        } elseif ($this->cookieContext->getShopContext() === Shop::CONTEXT_SHOP) {
            $shopContext = Shop::CONTEXT_SHOP;
            if ($this->employeeContext->hasAuthorizationOnShop($this->cookieContext->getShopId())) {
                $shopId = $this->cookieContext->getShopId();
            } else {
                $shopId = $this->employeeContext->getDefaultShopId();
            }
        }

        // Set the shop context with appropriate value
        if ($shopContext === Shop::CONTEXT_SHOP) {
            $this->shopContextBuilder->setShopContext($shopId);
        } elseif ($shopContext === Shop::CONTEXT_GROUP) {
            $this->shopContextBuilder->setShopGroupContext($shopGroupId);
        } else {
            $this->shopContextBuilder->setAllShopsContext();
        }

        // In all cases a shop must be set for the context even if it's the default one
        if (!$shopId) {
            $shopId = (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops());
        }
        $this->shopContextBuilder->setShopId($shopId);
    }
}
