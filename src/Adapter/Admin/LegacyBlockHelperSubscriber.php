<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Admin;

use Exception;
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
     * @throws Exception
     */
    public function renderKpi(RenderingHookEvent $event)
    {
        if (!array_key_exists('kpi_controller', $event->getHookParameters())) {
            throw new Exception('The legacy_kpi hook need a kpi_controller parameter (legacy controller full class name).');
        }

        $controller = $event->getHookParameters()['kpi_controller'];
        $controller = new $controller('new-theme');
        $renderKpis = $controller->renderKpis() !== null ? $controller->renderKpis() : [];

        $event->setContent($renderKpis);
    }
}
