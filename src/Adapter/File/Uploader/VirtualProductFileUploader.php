<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
