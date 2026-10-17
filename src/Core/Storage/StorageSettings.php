<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Storage;

/**
 * Names of the storages provided by the core.
 *
 * Each one has its own purpose, so that a shop can move its product images to object storage
 * while keeping the rest on the local disk. Modules needing another storage can register their
 * own instead of writing files anywhere in the project.
 */
final class StorageSettings
{
    /**
     * Project root. Fallback for code that still works with project wide paths.
     */
    public const STORAGE_DEFAULT = 'default';

    /**
     * Shop images: products, categories, manufacturers, suppliers, stores...
     */
    public const STORAGE_IMAGES = 'images';

    /**
     * Files uploaded by customers, such as product customizations.
     */
    public const STORAGE_UPLOADS = 'uploads';

    /**
     * Files sold as virtual products, which must never be publicly readable.
     */
    public const STORAGE_DOWNLOADS = 'downloads';
}
