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

namespace PrestaShop\PrestaShop\Adapter\Addons\Downloader;

use PrestaShop\PrestaShop\Core\Addon\Downloader\ThemeDownloaderInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use RuntimeException;
use Tools;

/**
 * Class ThemeDownloader
 */
final class ThemeDownloader implements ThemeDownloaderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var string
     */
    private $customerThemeListCacheFile;

    /**
     * @var string
     */
    private $mustHaveThemeListCacheFile;

    /**
     * @var string
     */
    private $uploadedThemesDirName;

    /**
     * @param ConfigurationInterface $configuration
     * @param string $customerThemeListCacheFile
     * @param string $mustHaveThemeListCacheFile
     * @param string $uploadedThemesDirName
     */
    public function __construct(
        ConfigurationInterface $configuration,
        $customerThemeListCacheFile,
        $mustHaveThemeListCacheFile,
        $uploadedThemesDirName
    ) {
        $this->configuration = $configuration;
        $this->customerThemeListCacheFile = $customerThemeListCacheFile;
        $this->mustHaveThemeListCacheFile = $mustHaveThemeListCacheFile;
        $this->uploadedThemesDirName = $uploadedThemesDirName;
    }

    /**
     * {@inheritdoc}
     */
    public function download()
    {
        $customerThemeListCacheFilePath =
            $this->configuration->get('_PS_ROOT_DIR_') . $this->customerThemeListCacheFile;

        if (!$this->isFresh($this->customerThemeListCacheFile, 86400)) {
            file_put_contents($customerThemeListCacheFilePath, Tools::addonsRequest('customer_themes'));
        }

        $customerThemeList = file_get_contents($customerThemeListCacheFilePath);

        if (!empty($customerThemeList) && $customerThemeListXml = @simplexml_load_string($customerThemeList)) {
            foreach ($customerThemeListXml->theme as $theme) {
                $themeIds = Tools::unSerialize($this->configuration->get('PS_ADDONS_THEMES_IDS'));

                if (!is_array($themeIds) || (is_array($themeIds) && !in_array((string) $theme->id, $themeIds))) {
                    $zipContent = Tools::addonsRequest(
                        'module',
                        [
                            'id_module' => pSQL($theme->id),
                            'username_addons' => pSQL(trim(\Context::getContext()->cookie->username_addons)),
                            'password_addons' => pSQL(trim(\Context::getContext()->cookie->password_addons)),
                        ]
                    );

                    $sandbox = $this->configuration->get('_PS_CACHE_DIR_') . 'sandbox' . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR;

                    if (!mkdir($sandbox) && !is_dir($sandbox)) {
                        throw new RuntimeException(sprintf('Directory "%s" was not created', $sandbox));
                    }

                    file_put_contents($sandbox . $theme->getName() . '.zip', $zipContent);

                    if ($this->extractTheme($sandbox . $theme->getName() . '.zip', $sandbox)) {
                        if ($theme_directory = $this->installTheme($this->uploadedThemesDirName, $sandbox, false)) {
                            $themeIds[$theme_directory] = (string) $theme->id;
                        }
                    }

                    Tools::deleteDirectory($sandbox);
                }

                $this->configuration->set('PS_ADDONS_THEMES_IDS', serialize($themeIds));
            }
        }
    }

    /**
     * @param string $file
     * @param int $timeout
     *
     * @return bool
     */
    private function isFresh($file, $timeout = 604800)
    {
        if (($time = @filemtime(_PS_ROOT_DIR_ . $file)) && filesize(_PS_ROOT_DIR_ . $file) > 0) {
            return (time() - $time) < $timeout;
        }

        return false;
    }
}
