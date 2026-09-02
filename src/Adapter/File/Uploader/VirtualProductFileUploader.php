<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use ErrorException;
use PrestaShop\PrestaShop\Adapter\File\Validator\VirtualProductFileValidator;
use PrestaShop\PrestaShop\Core\File\Exception\CannotUnlinkFileException;
use PrestaShop\PrestaShop\Core\File\Exception\FileUploadException;
use ProductDownload as VirtualProductFile;

/**
 * Uploads file for virtual product
 * Legacy object ProductDownload is referred as VirtualProductFile in Core
 */
class VirtualProductFileUploader
{
    /**
     * @var VirtualProductFileValidator
     */
    private $virtualProductFileValidator;

    /**
     * @var string
     */
    private $virtualProductFileDir;

    /**
     * @param VirtualProductFileValidator $virtualProductFileValidator
     * @param string $downloadDir
     */
    public function __construct(
        VirtualProductFileValidator $virtualProductFileValidator,
        string $downloadDir
    ) {
        $this->virtualProductFileValidator = $virtualProductFileValidator;
        $this->virtualProductFileDir = $downloadDir;
    }

    /**
     * @param string $filePath file to upload $filePath
     *
     * @return string uploaded file path
     */
    public function upload(string $filePath): string
    {
        $this->virtualProductFileValidator->validate($filePath);
        $destination = $this->virtualProductFileDir . VirtualProductFile::getNewFilename();

        $this->copyFile($filePath, $destination);
        $this->removeFile($filePath);

        return $destination;
    }

    /**
     * @param string $filename
     */
    public function remove(string $filename): void
    {
        $this->removeFile($this->virtualProductFileDir . $filename);
    }

    /**
     * @param string $newFilepath
     * @param string|null $oldFilename
     *
     * @return string
     */
    public function replace(string $newFilepath, ?string $oldFilename): string
    {
        if ($oldFilename) {
            $this->removeFile($this->virtualProductFileDir . $oldFilename);
        }

        return $this->upload($newFilepath);
    }

    /**
     * @param string $filePath
     * @param string $destination
     *
     * @throws FileUploadException
     */
    private function copyFile(string $filePath, string $destination): void
    {
        try {
            copy($filePath, $destination);
        } catch (ErrorException $e) {
            throw new FileUploadException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $filePath
     *
     * @throws CannotUnlinkFileException
     */
    private function removeFile(string $filePath): void
    {
        try {
            unlink($filePath);
        } catch (ErrorException $e) {
            throw new CannotUnlinkFileException($e->getMessage(), 0, $e);
        }
    }
}
