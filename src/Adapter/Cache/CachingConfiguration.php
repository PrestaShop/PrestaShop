<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache;

use PrestaShop\PrestaShop\Adapter\Configuration\PhpParameters;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Caching configuration for a Shop.
 */
class CachingConfiguration implements DataConfigurationInterface
{
    /**
     * @var MemcacheServerManager
     */
    private $memcacheServerManager;

    /**
     * @var PhpParameters
     */
    private $phpParameters;

    /**
     * @var CacheClearerInterface
     */
    private $symfonyCacheClearer;

    /**
     * @var bool check if the caching is enabled
     */
    private $isCachingEnabled;

    /**
     * @var string the selected Caching system: 'CacheApc' for instance
     */
    private $cachingSystem;

    /**
     * @param MemcacheServerManager $memcacheServerManager
     * @param PhpParameters $phpParameters
     * @param CacheClearerInterface $symfonyCacheClearer
     * @param bool $isCachingEnabled
     * @param string $cachingSystem
     */
    public function __construct(
        MemcacheServerManager $memcacheServerManager,
        PhpParameters $phpParameters,
        CacheClearerInterface $symfonyCacheClearer,
        $isCachingEnabled,
        $cachingSystem
    ) {
        $this->memcacheServerManager = $memcacheServerManager;
        $this->phpParameters = $phpParameters;
        $this->symfonyCacheClearer = $symfonyCacheClearer;
        $this->isCachingEnabled = $isCachingEnabled;
        $this->cachingSystem = $cachingSystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'use_cache' => $this->isCachingEnabled,
            'caching_system' => $this->cachingSystem,
            'servers' => $this->memcacheServerManager->getServers(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $errors = $this->updatePhpCacheConfiguration($configuration);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['use_cache'],
            $configuration['caching_system'],
            $configuration['servers']
        );
    }

    /**
     * Update the Php configuration for Cache feature and system.
     *
     * @return array the errors list during the update operation
     */
    private function updatePhpCacheConfiguration(array $configuration)
    {
        $errors = [];

        if (
            $configuration['use_cache'] !== $this->isCachingEnabled
            && null !== $configuration['caching_system']
        ) {
            $this->phpParameters->setProperty('parameters.ps_cache_enable', $configuration['use_cache']);
        }

        if (
            null !== $configuration['caching_system']
            && $configuration['caching_system'] !== $this->cachingSystem
        ) {
            $this->phpParameters->setProperty('parameters.ps_caching', $configuration['caching_system']);
        }

        if (false === $this->phpParameters->saveConfiguration()) {
            $errors[] = [
                'key' => 'The settings file cannot be overwritten.',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        }

        $this->symfonyCacheClearer->clear();

        return $errors;
    }
}
