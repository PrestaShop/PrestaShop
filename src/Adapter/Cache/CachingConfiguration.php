<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter\Cache;

use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use PrestaShop\PrestaShop\Adapter\Configuration\PhpParameters;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Caching configuration for a Shop
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
     * @var CacheClearer
     */
    private $cacheClearer;

    /**
     * @var bool Check if the caching is enabled.
     */
    private $isCachingEnabled;

    /**
     * @var string The selected Caching system: 'CacheApc' for instance.
     */
    private $cachingSystem;

    public function __construct(
        MemcacheServerManager $memcacheServerManager,
        PhpParameters $phpParameters,
        CacheClearer $cacheClearer,
        $isCachingEnabled,
        $cachingSystem
    ) {
        $this->memcacheServerManager = $memcacheServerManager;
        $this->phpParameters = $phpParameters;
        $this->cacheClearer = $cacheClearer;
        $this->isCachingEnabled = $isCachingEnabled;
        $this->cachingSystem = $cachingSystem;
    }

    /**
     * @{inheritdoc}
     */
    public function getConfiguration()
    {
        return array(
            'use_cache' => $this->isCachingEnabled,
            'caching_system' => $this->cachingSystem,
            'servers' => $this->memcacheServerManager->getServers(),
        );
    }

    /**
     * @{inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();

        if ($this->validateConfiguration($configuration)) {
            $errors = $this->updatePhpCacheConfiguration($configuration);
        }

        return $errors;
    }

    /**
     * @{inheritdoc}
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
     * @return array the errors list during the update operation.
     */
    private function updatePhpCacheConfiguration(array $configuration)
    {
        $errors = array();

        if (
            $configuration['use_cache'] !== $this->isCachingEnabled
            && !is_null($configuration['caching_system'])
        ) {
            $this->phpParameters->setProperty('parameters.ps_cache_enable', $configuration['use_cache']);
        }

        if (
            !is_null($configuration['caching_system'])
            && $configuration['caching_system'] !== $this->cachingSystem
        ) {
            $this->phpParameters->setProperty('parameters.ps_caching', $configuration['caching_system']);
        }

        if (false === $this->phpParameters->saveConfiguration()) {
            $errors[] = array(
                'key' => 'The settings file cannot be overwritten.',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => array(),
            );
        }

        $this->cacheClearer->clearSymfonyCache();

        return $errors;
    }
}
