<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image;

use ImageManager;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageSizeException;

class ProductImageFileValidator extends ImageValidator
{
    private const MEGABYTE_IN_BYTES = 1048576;

    /**
     * @var DataConfigurationInterface
     */
    private $uploadQuotaConfiguration;

    public function __construct(
        int $maxUploadSizeInBytes,
        DataConfigurationInterface $uploadQuotaConfiguration
    ) {
        parent::__construct($maxUploadSizeInBytes);
        $this->uploadQuotaConfiguration = $uploadQuotaConfiguration;
    }

    /**
     * @param string $filePath
     *
     * @throws UploadedImageSizeException
     */
    public function assertFileUploadLimits(string $filePath): void
    {
        $size = new DecimalNumber((string) filesize($filePath));
        $maxUploadSizeBytes = new DecimalNumber((string) $this->maxUploadSize);
        $maxUploadQuotaMegaBytes = new DecimalNumber((string) $this->uploadQuotaConfiguration->getConfiguration()['max_size_product_image']);
        $maxUploadQuotaBytes = $maxUploadQuotaMegaBytes->times(new DecimalNumber((string) self::MEGABYTE_IN_BYTES));

        if ($maxUploadQuotaBytes->isLowerThan($maxUploadSizeBytes)) {
            // if upload limit which is set in BO settings is less than php.ini upload limit, then we check according to that value
            $maxUploadSizeBytes = $maxUploadQuotaBytes;
        }

        if ($maxUploadSizeBytes->isGreaterThanZero() && $size->isGreaterThan($maxUploadSizeBytes)) {
            throw UploadedImageSizeException::build((int) (string) $maxUploadSizeBytes);
        }

        if (!ImageManager::checkImageMemoryLimit($filePath)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }
    }
}
