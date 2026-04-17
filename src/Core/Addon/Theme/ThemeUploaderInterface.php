<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface ThemeUploaderInterface
 */
interface ThemeUploaderInterface
{
    /**
     * @param UploadedFile $uploadedTheme
     *
     * @return string Path to uploaded theme
     */
    public function upload(UploadedFile $uploadedTheme);
}
