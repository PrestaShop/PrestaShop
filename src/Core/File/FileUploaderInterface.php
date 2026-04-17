<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\File;

interface FileUploaderInterface
{
    /**
     * Upload a file
     *
     * @return array{id: string, file_name: string, mime_type: string}
     */
    public function upload($file): array;
}
