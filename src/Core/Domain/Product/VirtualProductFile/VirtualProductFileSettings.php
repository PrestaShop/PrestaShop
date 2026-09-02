<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile;

class VirtualProductFileSettings
{
    /**
     * Following constants defines maximum characters length constraints for corresponding fields
     */
    public const MAX_DISPLAY_FILENAME_LENGTH = 255;
    public const MAX_FILENAME_LENGTH = 255;
    public const MAX_ACCESSIBLE_DAYS_LIMIT = 9999999999;
    public const MAX_DOWNLOAD_TIMES_LIMIT = 9999999999;

    /**
     * Class not supposed to be initialized, it only serves as static storage
     */
    private function __construct()
    {
    }
}
