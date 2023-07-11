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

use PrestaShop\PrestaShop\Core\Context\CookieContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CookieContextListener
{
    public function __construct(
        private readonly CookieContextBuilder $shopCookieBuilder,
        private readonly ShopConfigurationInterface $configuration
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $allShopsConstraint = ShopConstraint::allShops();
        $adminCookieLifetime = $this->configuration->get('PS_COOKIE_LIFETIME_BO', null, $allShopsConstraint);
        if ($adminCookieLifetime > 0) {
            $adminCookieLifetime = time() + (max($adminCookieLifetime, 1) * 3600);
        }
        $forceSsl = (bool) $this->configuration->get('PS_SSL_ENABLED', null, $allShopsConstraint)
            && (bool) $this->configuration->get('PS_SSL_ENABLED_EVERYWHERE', null, $allShopsConstraint);

        $this->shopCookieBuilder
            ->setCookieName('psAdmin')
            ->setCookieLifetime($adminCookieLifetime)
            ->setForceSsl($forceSsl)
            ->setCookiePath('')
        ;
    }
}
