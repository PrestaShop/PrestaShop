<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;

class CccReducerCore
{
    use PrestaShop\PrestaShop\Adapter\Assets\AssetUrlGeneratorTrait;
    /** @var string */
    private $cacheDir;
    /** @var Filesystem */
    protected $filesystem;
    /** @var ConfigurationInterface */
    public $configuration;

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
