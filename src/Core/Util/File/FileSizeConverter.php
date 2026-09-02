<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\File;

/**
 * Converts int value to formatted file size value
 */
class FileSizeConverter
{
    /**
     * @param int $bytes
     * @param int $precision
     *
     * @return string
     */
    public function convert(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        if ($bytes >= 1024) {
            $bytes /= 1024 ** $pow;
            $bytes = number_format(round($bytes, $precision), 2, '.', '');
        }

        return $bytes . $units[$pow];
    }
}
