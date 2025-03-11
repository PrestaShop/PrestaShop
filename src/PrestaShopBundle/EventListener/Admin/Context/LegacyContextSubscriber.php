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

use PrestaShop\PrestaShop\Core\Context\LegacyContextBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This listener is responsible for calling every LegacyContextBuilderInterface services and
 * ask them to build the legacy context. This interface is usually implement by the recent
 * context builders that are already responsible for building the recent split context services.
 *
 * Since they already handle the new context it makes sense to give them the responsibility of keeping
 * the backward compatibility on legacy context, so they also fill the legacy context fields based on
 * the settings that ere provided to them, this way we keep a single source of truth.
 *
 * This listener is only executed on kernel.controller event, this way we are sure that a Symfony controller
 * has been found, so this listener shouldn't mess with legacy pages.
 *
 * It is only used for the Back-Office/Admin application.
 */
class LegacyContextSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable|LegacyContextBuilderInterface[] $legacyBuilders
     */
    public function __construct(
        private readonly iterable $legacyBuilders
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Build legacy context early in the process, so it is available for AdminController::init that is executed
            // early in the process during the route matching in LegacyRouterChecker
            KernelEvents::REQUEST => 'buildLegacyContext',
            // Rebuild the legacy context after all request listeners executed and LegacyRouterChecker executed AdminController::init
            // in case the legacy controller changed come context value
            KernelEvents::CONTROLLER => 'buildLegacyContext',
        ];
    }

    public function buildLegacyContext(KernelEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        foreach ($this->legacyBuilders as $legacyBuilder) {
            $legacyBuilder->buildLegacyContext();
        }
    }
}
