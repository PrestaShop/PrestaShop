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

use PrestaShop\PrestaShop\Core\Context\LegacyControllerContextBuilder;
use PrestaShopBundle\EventListener\ExternalApiTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/36
 */
class LegacyControllerContextListener
{
    use ExternalApiTrait;

    public function __construct(
        private readonly LegacyControllerContextBuilder $legacyControllerContextBuilder,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || $this->isExternalApiRequest($event->getRequest())) {
            return;
        }

        $controllerName = $this->getControllerName($event->getRequest());
        $this->legacyControllerContextBuilder->setControllerName($controllerName);

        // Optional redirection url
        if ($event->getRequest()->query->has('back')) {
            $this->legacyControllerContextBuilder->setRedirectionUrl($event->getRequest()->query->get('back'));
        }
    }

    private function getControllerName(?Request $request): string
    {
        $controllerName = 'AdminController';

        if ($request->attributes->has('_legacy_controller')) {
            $controllerName = $request->attributes->get('_legacy_controller');
        } elseif ($request->query->has('controller')) {
            $controllerName = $request->query->get('controller');
        }

        return $controllerName;
    }
}
