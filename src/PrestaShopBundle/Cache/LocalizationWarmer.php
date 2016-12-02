<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Cache;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tools;

/**
 * Class LocalizationCacheWarmer.
 *
 * This cache warmer is also called in bootstrap of the application
 *
 * Put in cache localization values to be used in both back and front offices (i.e "17fr.xml")
 *
 * Updates the version of file provided by PrestaShop installation.
 */
class LocalizationCacheWarmer implements CacheWarmerInterface
{
    const API_PRESTASHOP_ENTRY_POINT = 'http://api.prestashop.com/localization/';

    private $version;
    private $country;
    private $filesystem;

    public function __construct($country, Filesystem $filesystem)
    {
        $this->version = _PS_VERSION_;
        $this->country = $country;
        $this->filesystem = $filesystem;
    }

    public function warmUp($cacheDir)
    {
        $this->filesystem->mkdir($cacheDir);

        $pathCacheFile = _PS_CACHE_DIR_.'sandbox'.DIRECTORY_SEPARATOR.$this->version.$this->country.'.xml';

        if (is_file($pathCacheFile)) {
            $localizationFileContent = file_get_contents($pathCacheFile);
        } else {
            $localizationFileContent = @Tools::file_get_contents(self::API_PRESTASHOP_ENTRY_POINT.$this->version.'/'.$this->country.'.xml');
            if (!@simplexml_load_string($localizationFileContent)) {
                $localizationFileContent = false;
            }
            if (!$localizationFileContent) {
                $localizationFile = _PS_ROOT_DIR_.'/localization/default.xml';

                if (file_exists(_PS_ROOT_DIR_.'/localization/'.$this->country.'.xml')) {
                    $localizationFile = _PS_ROOT_DIR_.'/localization/'.$this->country.'.xml';
                }

                $localizationFileContent = file_get_contents($localizationFile);
            }

            $this->filesystem->dumpFile($cacheDir, $localizationFileContent);
        }

        return $localizationFileContent;
    }

    public function isOptional()
    {
        return false;
    }
}
