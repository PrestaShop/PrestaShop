<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Adapter\Tools;

/**
 * This class manages CCC features configuration for a Shop
 */
class CombineCompressCacheConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var string Absolute path to the theme directory
     */
    private $themePath;

    /**
     * @var string Current active theme name
     */
    private $themeName;

    public function __construct(
        Configuration $configuration,
        Filesystem $filesystem,
        Tools $tools,
        $themePath,
        $themeName
    ) {
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;
        $this->tools = $tools;
        $this->themePath = $themePath;
        $this->themeName = $themeName;
    }

    /**
     * @{inheritdoc}
     */
    public function getConfiguration()
    {
        return array(
            'smart_cache_css' => $this->configuration->get('PS_CSS_THEME_CACHE'),
            'smart_cache_js' => $this->configuration->get('PS_JS_THEME_CACHE'),
            'apache_optimization' => $this->configuration->get('PS_HTACCESS_CACHE_CONTROL'),
        );
    }

    /**
     * @{inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();

        if ($this->validateConfiguration($configuration)) {
            if ($configuration['smart_cache_css'] || $configuration['smart_cache_js']) {
                // Manage JS & CSS Smart cache
                if ($this->createThemeCacheFolder()) {
                    $this->updateCachesVersionsIfNeeded($configuration);
                } else {
                    $errors[] = array(
                        'key' => 'To use Smarty Cache, the directory %directorypath% must be writable.',
                        'domain' => 'Admin.Advparameters.Notification',
                        'parameters' => array(
                            '%directorypath%' => $this->getThemeCacheFolder()
                        )
                    );
                }
            }

            // Manage Apache optimization
            $apacheError = $this->manageApacheOptimization((bool) $configuration['apache_optimization']);

            if (count($apacheError) > 0) {
                $errors[] = $apacheError;
            }
        }

        return $errors;
    }

    /**
     * @{inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(
            array(
                'smart_cache_css',
                'smart_cache_js',
                'apache_optimization',
            )
        );
        $resolver->resolve($configuration);

        return true;
    }

    /**
     * @return string Absolute path the the current active theme
     */
    private function getThemeCacheFolder()
    {
        return $this->themePath . '/' . $this->themeName . '/cache/';
    }

    /**
     * Creates Cache folder for the active theme
     * @return bool
     */
    private function createThemeCacheFolder()
    {
        try {
            $folder = $this->getThemeCacheFolder();
            $this->filesystem->mkdir($folder, '0777');

            return true;
        } catch (IOExceptionInterface $e) {
            return false;
        }
    }

    /**
     * Update Cache version of assets if needed
     * @param array the configuration
     */
    private function updateCachesVersionsIfNeeded(array $configuration)
    {
        $cacheCSS = $configuration['smart_cache_css'];
        $currentCacheCSS = $this->configuration->get('PS_CSS_THEME_CACHE');

        $cacheJS = $configuration['smart_cache_js'];
        $currentCacheJS = $this->configuration->get('PS_JS_THEME_CACHE');

        if ($cacheCSS !== $currentCacheCSS) {
            $cssCacheVersion = $this->configuration->get('PS_CCCCSS_VERSION') + 1;
            $this->configuration->set('PS_CCCCSS_VERSION', $cssCacheVersion);
            $this->configuration->set('PS_CSS_THEME_CACHE', $cacheCSS);
        }

        if ($cacheJS !== $currentCacheJS) {
            $jsCacheVersion = $this->configuration->get('PS_CCCJS_VERSION') + 1;
            $this->configuration->set('PS_CCCCSS_VERSION', $jsCacheVersion);
            $this->configuration->set('PS_JS_THEME_CACHE', $cacheJS);
        }
    }

    /**
     * Creates .htaccess if Apache optimization feature is enabled
     * @param bool $enabled
     * @return array not empty in case of error
     */
    private function manageApacheOptimization($enabled)
    {
        $errors = array();
        $isCurrentlyEnabled = (bool) $this->configuration->get('PS_HTACCESS_CACHE_CONTROL');

        // feature activation
        if (false === $isCurrentlyEnabled && true === $enabled) {
            if ($this->tools->generateHtaccess()) {
                $this->configuration->set('PS_HTACCESS_CACHE_CONTROL', true);
            } else {
                $errors = array(
                    'key' => 'Before being able to use this tool, you need to:[1][2]Create a blank .htaccess in your root directory.[/2][2]Give it write permissions (CHMOD 666 on Unix system).[/2][/1]',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' =>
                    array(
                        '[1]' => '<ul>',
                        '[/1]' => '</ul>',
                        '[2]' => '<li>',
                        '[/2]' => '</li>',
                    ),
                );
                $this->configuration->set('PS_HTACCESS_CACHE_CONTROL', false);
            }
        }

        if (true === $isCurrentlyEnabled && false === $enabled) {
            $this->configuration->set('PS_HTACCESS_CACHE_CONTROL', false);
        }

        return $errors;
    }
}
