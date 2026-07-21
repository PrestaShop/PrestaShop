<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;

final class StoreImageUploader extends AbstractImageUploader
{
    /**
     * Uploads the store image from a file path, so it can be used
     * from both the back office form and the Admin API.
     *
     * @throws ImageOptimizationException
     * @throws MemoryLimitException
     */
    public function upload(int $storeId, string $filePath): void
    {
        $this->uploadFromTemp($filePath, _PS_STORE_IMG_DIR_ . $storeId . '.jpg');

        $this->generateDifferentSize($storeId, _PS_STORE_IMG_DIR_, 'stores');
    }
}
