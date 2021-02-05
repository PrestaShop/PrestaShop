<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\File;

use ImageManager;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\File\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\File\Exception\InvalidFileException;
use PrestaShop\PrestaShop\Core\File\Exception\MaximumSizeExceededException;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @var int
     */
    protected $maximumSize;

    /**
     * @var string
     */
    protected $downloadDirectory;

    /**
     * @param string $downloadDirectory
     * @param int $maximumSize
     */
    public function __construct(
        string $downloadDirectory,
        int $maximumSize
    ) {
        $this->downloadDirectory = $downloadDirectory;
        $this->maximumSize = $maximumSize;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($file): array
    {
        if (is_array($file)) {
            return $this->uploadFromHttpPost($file);
        }

        if (is_string($file)) {
            return $this->uploadFromBinaryFile($file);
        }

        throw new InvalidArgumentException();
    }

    /**
     * Validate file size
     *
     * @param array $file
     *
     * @throws InvalidFileException
     * @throws MaximumSizeExceededException
     */
    protected function validateSize(array $file): void
    {
        if (!isset($file['size'])) {
            throw new InvalidFileException();
        }

        if ($file['size'] > $this->maximumSize) {
            throw new MaximumSizeExceededException((string) $file['size']);
        }
    }

    /**
     * Validate if file is an uploaded file
     *
     * @param array $file
     *
     * @throws InvalidFileException
     * @throws FileUploadException
     */
    protected function validateIsUploadedFile(array $file): void
    {
        if (!isset($file['tmp_name'])
            || !isset($file['type'])
            || !isset($file['name'])
        ) {
            throw new InvalidFileException();
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new FileUploadException();
        }
    }

    /**
     * Generate file name from uniqid
     *
     * @return string
     */
    protected function generateFileName(): string
    {
        do {
            $uniqid = sha1(uniqid()); // must be a sha1
        } while (file_exists($this->downloadDirectory . $uniqid));

        return $uniqid;
    }

    /**
     * Upload file from Http Post
     *
     * @param array $file the $_FILES content
     *
     * @return array<string, string>
     *
     * @throws FileUploadException
     */
    public function uploadFromHttpPost(array $file): array
    {
        $this->validateSize($file);
        $this->validateIsUploadedFile($file);

        $fileName = $this->generateFileName();
        if (!move_uploaded_file($file['tmp_name'], $this->downloadDirectory . $fileName)) {
            throw new FileUploadException();
        }

        return [
            'id' => $fileName,
            'file_name' => $file['name'],
            'mime_type' => $file['type'],
        ];
    }

    /**
     * Upload file from binary request
     *
     * @param string $content The binary string
     *
     * @return array<string, string>
     *
     * @throws FileUploadException
     */
    public function uploadFromBinaryFile(string $content): array
    {
        $file = [
            // It returns the number of bytes rather than the number of characters
            'size' => strlen($content),
        ];
        $this->validateSize($file);

        $fileName = $this->generateFileName();

        // Ignore warning, we only need to know if everything is ok
        if (@file_put_contents($this->downloadDirectory . $fileName, $content) === false) {
            throw new FileUploadException();
        }

        return [
            'id' => $fileName,
            'file_name' => uniqid('', true),
            'mime_type' => ImageManager::getMimeType($this->downloadDirectory . $fileName),
        ];
    }
}
