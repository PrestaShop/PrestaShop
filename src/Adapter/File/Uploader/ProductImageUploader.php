<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ProductImageUploaderInterface;

final class ProductImageUploader implements ProductImageUploaderInterface
{
    /**
     * @var UploadSizeConfigurationInterface
     */
    private $uploadSizeConfiguration;

    /**
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     */
    public function __construct(
        UploadSizeConfigurationInterface $uploadSizeConfiguration
    ) {
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(
        //@todo: some arguments might not be needed for product
        string $filePath,
        string $destinationPath,
        int $fileSize
    ): void {
        $this->checkFileAllowedForUpload($fileSize);
//@todo: upload file and delete old one
    }

    /**
     * @throws ImageConstraintException
     */
    private function checkFileAllowedForUpload(int $fileSize): void
    {
        $maxFileSize = $this->uploadSizeConfiguration->getMaxUploadSizeInBytes();

        if ($maxFileSize > 0 && $fileSize > $maxFileSize) {
            throw new ImageConstraintException(
                sprintf('Max file size allowed is "%s" bytes. Uploaded file size is "%s".', $maxFileSize, $fileSize),
                ImageConstraintException::INVALID_FILE_SIZE
            );
        }
    }
}
