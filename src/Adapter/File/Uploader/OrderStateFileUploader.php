<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateUploadFailedException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\OrderStateFileUploaderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Uploads order state file
 */
class OrderStateFileUploader implements OrderStateFileUploaderInterface
{
    /**
     * @var UploadSizeConfigurationInterface
     */
    protected $uploadSizeConfiguration;

    /**
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     */
    public function __construct(UploadSizeConfigurationInterface $uploadSizeConfiguration)
    {
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $throwExceptionOnFailure
     *
     * @throws OrderStateConstraintException
     * @throws OrderStateUploadFailedException
     */
    public function upload(
        string $filePath,
        int $id,
        int $fileSize,
        bool $throwExceptionOnFailure = true
    ): void {
        $this->checkFileAllowedForUpload($fileSize);
        $this->uploadFile($filePath, $id);
    }

    /**
     * @param string $filePath
     * @param int $id
     *
     * @throws OrderStateUploadFailedException
     */
    protected function uploadFile(string $filePath, int $id): void
    {
        try {
            move_uploaded_file($filePath, _PS_ORDER_STATE_IMG_DIR_ . $id . '.gif');
        } catch (FileException $e) {
            throw new OrderStateUploadFailedException(sprintf('Failed to copy the file %s.', $filePath), 0, $e);
        }
    }

    /**
     * @param int $fileSize
     *
     * @throws OrderStateConstraintException
     */
    protected function checkFileAllowedForUpload(int $fileSize): void
    {
        $maxFileSize = $this->uploadSizeConfiguration->getMaxUploadSizeInBytes();

        if ($maxFileSize > 0 && $fileSize > $maxFileSize) {
            throw new OrderStateConstraintException(
                sprintf('Max file size allowed is "%s" bytes. Uploaded file size is "%s".', $maxFileSize, $fileSize),
                OrderStateConstraintException::INVALID_FILE_SIZE
            );
        }
    }
}
