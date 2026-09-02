<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Cache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class CacheWarmer implements CacheWarmerInterface
{
    private $fileSystem;

    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function warmUp($cacheDir)
    {
        $legacyDirs = [
            $cacheDir . DIRECTORY_SEPARATOR . 'cachefs',
            $cacheDir . DIRECTORY_SEPARATOR . 'purifier',
            $cacheDir . DIRECTORY_SEPARATOR . 'push',
            $cacheDir . DIRECTORY_SEPARATOR . 'sandbox',
            $cacheDir . DIRECTORY_SEPARATOR . 'tcpdf',
        ];

        $this->fileSystem->mkdir($legacyDirs);

        return [];
    }

    public function isOptional()
    {
        return false;
    }
}
