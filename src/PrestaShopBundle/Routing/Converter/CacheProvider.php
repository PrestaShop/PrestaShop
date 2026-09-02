<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Converter;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CacheProvider.
 */
class CacheProvider extends AbstractLegacyRouteProvider implements CacheCleanerInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var LegacyRouteProviderInterface
     */
    private $legacyRouteProvider;

    /**
     * @var CacheKeyGeneratorInterface
     */
    private $cacheKeyGenerator;

    /**
     * @var LegacyRoute[]
     */
    private $legacyRoutes;

    public function __construct(
        LegacyRouteProviderInterface $legacyRouteProvider,
        CacheItemPoolInterface $cache,
        CacheKeyGeneratorInterface $cacheKeyGenerator
    ) {
        $this->legacyRouteProvider = $legacyRouteProvider;
        $this->cache = $cache;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegacyRoutes()
    {
        if (null === $this->legacyRoutes) {
            $cacheItem = $this->cache->getItem($this->cacheKeyGenerator->getCacheKey());
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

    public function clearCache(): void
    {
        $this->cache->deleteItem($this->cacheKeyGenerator->getCacheKey());
    }

    /**
     * @param string $serializedLegacyRoutes
     *
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

    /**
     * @param LegacyRoute[] $legacyRoutes
     *
     * @return string
     */
    private function serializeLegacyRoutes(array $legacyRoutes)
    {
        $flattenRoutes = [];
        /** @var LegacyRoute $legacyRoute */
        foreach ($legacyRoutes as $legacyRoute) {
            $legacyLinks = [];
            foreach ($legacyRoute->getLegacyLinks() as $legacyLink) {
                if (empty($legacyLink['action'])) {
                    $legacyLinks[] = $legacyLink['controller'];
                } else {
                    $legacyLinks[] = $legacyLink['controller'] . ':' . $legacyLink['action'];
                }
            }
            $flattenRoutes[] = [
                'route_name' => $legacyRoute->getRouteName(),
                'legacy_links' => $legacyLinks,
                'legacy_parameters' => $legacyRoute->getRouteParameters(),
            ];
        }

        return json_encode($flattenRoutes);
    }
}
