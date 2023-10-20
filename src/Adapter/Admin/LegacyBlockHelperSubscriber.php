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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Admin;

use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds listeners to renderhook Twig function, to let adding legacy helpers like Kpi, etc...
 */
class LegacyBlockHelperSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The listeners array
     */
    public static function getSubscribedEvents()
    {
        return [
            'legacy_block_kpi' => ['renderKpi', 0],
        ];
    }

    /**
     * Renders a Kpi block for a given legacy controller name.
     *
     * @param RenderingHookEvent $event
     *
     * @throws \Exception
     */
    public function renderKpi(RenderingHookEvent $event)
    {
        if (!array_key_exists('kpi_controller', $event->getHookParameters())) {
            throw new \Exception('The legacy_kpi hook need a kpi_controller parameter (legacy controller full class name).');
        }

        $controller = $event->getHookParameters()['kpi_controller'];
        $controller = new $controller('new-theme');
        $renderKpis = $controller->renderKpis() !== null ? $controller->renderKpis() : [];

        $event->setContent($renderKpis);
    }
}
