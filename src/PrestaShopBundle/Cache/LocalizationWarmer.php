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

namespace PrestaShopBundle\Cache;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

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

        if (!is_dir($cacheDir)) {
            try {
                $fs->mkdir($cacheDir);
            } catch (IOExceptionInterface $e) {
                //@todo: log
            }
        }

        $path_cache_file = $cacheDir . $this->version . $this->country . '.xml';

        if (is_file($path_cache_file)) {
            $localization_file_content = file_get_contents($path_cache_file);
        } else {
            $localization_file = _PS_ROOT_DIR_ . '/localization/default.xml';

            if (file_exists(_PS_ROOT_DIR_ . '/localization/' . $this->country . '.xml')) {
                $localization_file = _PS_ROOT_DIR_ . '/localization/' . $this->country . '.xml';
            }

            $localization_file_content = file_get_contents($localization_file);

            try {
                $fs->dumpFile($path_cache_file, $localization_file_content);
            } catch (IOExceptionInterface $e) {
                //@todo: log
            }
        }

        return [$localization_file_content];
    }

    public function isOptional()
    {
        return false;
    }
}
