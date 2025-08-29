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
