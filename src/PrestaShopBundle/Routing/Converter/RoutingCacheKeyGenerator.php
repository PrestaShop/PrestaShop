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

namespace PrestaShopBundle\Routing\Converter;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class RoutingCacheKeyGenerator generates the cache key for the CacheProvider.
 * In prod environment the key never changes so you need to use cache:clear if you
 * need to update the cache.
 * In dev environment this class inspects the routing files to get their last modification
 * date which is then used to generate the key, hence each time a routing file is modified
 * the cache key changes so the cache is regenerated.
 */
class RoutingCacheKeyGenerator implements CacheKeyGeneratorInterface
{
    /**
     * @var array
     */
    private $coreRoutingPaths;

    /**
     * @var array
     */
    private $activeModulesPaths;

    /**
     * @var string
     */
    private $environment;

    /**
     * RoutingCacheKeyGenerator constructor.
     *
     * @param array $coreRoutingPaths
     * @param array $activeModulesPaths
     * @param string $environment
     */
    public function __construct(
        array $coreRoutingPaths,
        array $activeModulesPaths,
        $environment = 'dev'
    ) {
        $this->coreRoutingPaths = $coreRoutingPaths;
        $this->activeModulesPaths = $activeModulesPaths;
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getLastModifications()
    {
        $routingFiles = [];

        if (count($this->coreRoutingPaths)) {
            $finder = new Finder();
            $finder->files()->in($this->coreRoutingPaths);
            $finder->name('/\.(yml|yaml)$/');
            /** @var SplFileInfo $yamlFile */
            foreach ($finder as $yamlFile) {
                $routingFiles[$yamlFile->getPathname()] = $yamlFile->getMTime();
            }
        }

        foreach ($this->activeModulesPaths as $modulePath) {
            $extensions = ['yml', 'yaml'];
            foreach ($extensions as $extension) {
                $routingFile = $modulePath . '/config/routes.' . $extension;
                if (file_exists($routingFile)) {
                    $routingFiles[$routingFile] = filemtime($routingFile);
                }
            }
        }

        arsort($routingFiles);

        return $routingFiles;
    }

    /**
     * @return int|null
     */
    public function getLatestModificationTime()
    {
        $lastModifications = $this->getLastModifications();
        if (!count($lastModifications)) {
            return null;
        }

        return reset($lastModifications);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey()
    {
        $cacheKey = preg_replace('@\\\\@', '_', __NAMESPACE__);
        if ('prod' !== $this->environment) {
            $latestModification = $this->getLatestModificationTime();
            if (null !== $latestModification) {
                $cacheKey .= '_' . $latestModification;
            }
        }

        return $cacheKey;
    }
}
