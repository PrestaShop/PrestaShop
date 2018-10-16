<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Routing\Converter;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class CacheProvider.
 */
class CacheProvider extends AbstractLegacyRouteProvider
{
    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheId;

    /**
     * @var LegacyRouteProviderInterface
     */
    private $legacyRouteProvider;

    /**
     * @var LegacyRoute[]
     */
    private $legacyRoutes;

    public function __construct(
        LegacyRouteProviderInterface $legacyRouteProvider,
        AdapterInterface $cache
    ) {
        $this->legacyRouteProvider = $legacyRouteProvider;
        $this->cache = $cache;
        $this->cacheId = preg_replace('@\\\\@', '_', __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function getLegacyRoutes()
    {
        if (null === $this->legacyRoutes) {
            $cacheItem = $this->cache->getItem($this->cacheId);
            if (!$cacheItem->isHit()) {
                $this->legacyRoutes = $this->legacyRouteProvider->getLegacyRoutes();
                $cacheItem->set($this->serializeLegacyRoutes($this->legacyRoutes));
                $this->cache->save($cacheItem);
            } else {
                $this->legacyRoutes = $this->unserializeLegacyRoutes($cacheItem->get());
            }
        }

        return $this->legacyRoutes;
    }

    /**
     * @param LegacyRoute[] $legacyRoutes
     * @return string
     */
    private function serializeLegacyRoutes(array $legacyRoutes)
    {
        $flattenRoutes = [];
        /** @var LegacyRoute $legacyRoute */
        foreach ($legacyRoutes as $legacyRoute) {
            $legacyLinks = [];
            foreach ($legacyRoute->getLegacyLinks() as $legacyLink) {
                $legacyLinks[] = $legacyLink['controller'] . (!empty($legacyLink['action']) ? ':' . $legacyLink['action'] : '');
            }
            $flattenRoutes[] = [
                'route_name' => $legacyRoute->getRouteName(),
                'legacy_links' => $legacyLinks,
                'legacy_parameters' => $legacyRoute->getRouteParameters(),
            ];
        }

        return json_encode($flattenRoutes);
    }

    /**
     * @param string $serializedLegacyRoutes
     * @return LegacyRoute[]
     */
    private function unserializeLegacyRoutes($serializedLegacyRoutes)
    {
        $flattenRoutes = json_decode($serializedLegacyRoutes, true);

        $legacyRoutes = [];
        foreach ($flattenRoutes as $flattenRoute) {
            $legacyRoutes[$flattenRoute['route_name']] = new LegacyRoute(
                $flattenRoute['route_name'],
                $flattenRoute['legacy_links'],
                $flattenRoute['legacy_parameters']
            );
        }

        return $legacyRoutes;
    }
}
