<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Cache;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Tools;

class LocalizationWarmer implements CacheWarmerInterface
{
    private $version;
    private $country;

    public function __construct($version, $country)
    {
        $this->version = $version;
        $this->country = $country;
    }

    public function warmUp($cacheDir)
    {
        $fs = new Filesystem();

        if (is_dir($cacheDir)) {
            try {
                $fs->mkdir($cacheDir);
            } catch (IOExceptionInterface $e) {
                //@todo: log
            }
        }

        $path_cache_file = $cacheDir.$this->version.$this->country.'.xml';

        if (is_file($path_cache_file)) {
            $localization_file_content = file_get_contents($path_cache_file);
        } else {
            $localization_file_content = @Tools::file_get_contents('http://api.prestashop.com/localization/'.$this->version.'/'.$this->country.'.xml');
            if (!@simplexml_load_string($localization_file_content)) {
                $localization_file_content = false;
            }
            if (!$localization_file_content) {
                $localization_file = _PS_ROOT_DIR_.'/localization/default.xml';

                if (file_exists(_PS_ROOT_DIR_.'/localization/'.$this->country.'.xml')) {
                    $localization_file = _PS_ROOT_DIR_.'/localization/'.$this->country.'.xml';
                }

                $localization_file_content = file_get_contents($localization_file);
            }

            try {
                $fs->dumpFile($path_cache_file, $localization_file_content);
            } catch (IOExceptionInterface $e) {
                //@todo: log
            }
        }

        return $localization_file_content;
    }

    public function isOptional()
    {
        return false;
    }
}
