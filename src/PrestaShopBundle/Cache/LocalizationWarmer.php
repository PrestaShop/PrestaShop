<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            } catch (IOExceptionInterface) {
                // @todo: log
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
            } catch (IOExceptionInterface) {
                // @todo: log
            }
        }

        return [$localization_file_content];
    }

    public function isOptional()
    {
        return false;
    }
}
