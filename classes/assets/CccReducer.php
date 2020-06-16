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
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;

class CccReducerCore
{
    /** @var string */
    private $cacheDir;
    /** @var Filesystem */
    protected $filesystem;

    use PrestaShop\PrestaShop\Adapter\Assets\AssetUrlGeneratorTrait;

    /**
     * @param string $cacheDir
     * @param ConfigurationInterface $configuration
     * @param Filesystem $filesystem
     */
    public function __construct($cacheDir, ConfigurationInterface $configuration, Filesystem $filesystem)
    {
        $this->cacheDir = $cacheDir;
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;

        if (!is_dir($this->cacheDir)) {
            $this->filesystem->mkdir($this->cacheDir);
        }
    }

    /**
     * @param array $cssFileList
     *
     * @return array Same list, reduced
     */
    public function reduceCss($cssFileList)
    {
        $files = [];
        foreach ($cssFileList['external'] as $key => &$css) {
            if ('all' === $css['media'] && 'local' === $css['server']) {
                $files[] = $this->getPathFromUri($css['path']);
                unset($cssFileList['external'][$key]);
            }
        }

        $version = Configuration::get('PS_CCCCSS_VERSION');
        $cccFilename = 'theme-' . $this->getFileNameIdentifierFromList($files) . $version . '.css';
        $destinationPath = $this->cacheDir . $cccFilename;

        if (!$this->filesystem->exists($destinationPath)) {
            CssMinifier::minify($files, $destinationPath);
        }
        if (Tools::hasMediaServer()) {
            $relativePath = _THEMES_DIR_ . _THEME_NAME_ . '/assets/cache/' . $cccFilename;
            $destinationUri = Tools::getCurrentUrlProtocolPrefix() . Tools::getMediaServer($relativePath) . $relativePath;
        } else {
            $destinationUri = $this->getFQDN() . $this->getUriFromPath($destinationPath);
        }

        $cssFileList['external']['theme-ccc'] = [
            'id' => 'theme-ccc',
            'type' => 'external',
            'path' => $destinationPath,
            'uri' => $destinationUri,
            'media' => 'all',
            'priority' => StylesheetManager::DEFAULT_PRIORITY,
        ];

        return $cssFileList;
    }

    /**
     * @param array $jsFileList
     *
     * @return array Same list, reduced
     */
    public function reduceJs($jsFileList)
    {
        foreach ($jsFileList as $position => &$list) {
            $files = [];
            foreach ($list['external'] as $key => $js) {
                // We only CCC the file without 'refer' or 'async'
                if ('' === $js['attribute'] && 'local' === $js['server']) {
                    $files[] = $this->getPathFromUri($js['path']);
                    unset($list['external'][$key]);
                }
            }

            if (empty($files)) {
                // No file to CCC
                continue;
            }

            $version = Configuration::get('PS_CCCJS_VERSION');
            $cccFilename = $position . '-' . $this->getFileNameIdentifierFromList($files) . $version . '.js';
            $destinationPath = $this->cacheDir . $cccFilename;

            if (!$this->filesystem->exists($destinationPath)) {
                JsMinifier::minify($files, $destinationPath);
            }
            if (Tools::hasMediaServer()) {
                $relativePath = _THEMES_DIR_ . _THEME_NAME_ . '/assets/cache/' . $cccFilename;
                $destinationUri = Tools::getCurrentUrlProtocolPrefix() . Tools::getMediaServer($relativePath) . $relativePath;
            } else {
                $destinationUri = $this->getFQDN() . $this->getUriFromPath($destinationPath);
            }

            $cccItem = [];
            $cccItem[$position . '-js-ccc'] = [
                'id' => $position . '-js-ccc',
                'type' => 'external',
                'path' => $destinationPath,
                'uri' => $destinationUri,
                'priority' => JavascriptManager::DEFAULT_PRIORITY,
                'attribute' => '',
            ];
            $list['external'] = array_merge($cccItem, $list['external']);
        }

        return $jsFileList;
    }

    /**
     * @param string[] $files
     *
     * @return string
     */
    private function getFileNameIdentifierFromList(array $files)
    {
        return substr(sha1(implode('|', $files)), 0, 6);
    }
}
