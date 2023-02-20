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

namespace PrestaShop\PrestaShop\Adapter\Image;

use ImageManager;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageSizeException;

class ProductImageFileValidator extends ImageValidator
{
    private const MEGABYTE_IN_BYTES = 1000000;

    /**
     * @var DataConfigurationInterface
     */
    private $uploadQuotaConfiguration;

    public function __construct(
        int $maxUploadSize,
        DataConfigurationInterface $uploadQuotaConfiguration
    ) {
        parent::__construct($maxUploadSize);
        $this->uploadQuotaConfiguration = $uploadQuotaConfiguration;
    }

    /**
     * @param string $filePath
     *
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    public function assertFileUploadLimits(string $filePath): void
    {
        $size = filesize($filePath);
        $maxUploadSize = $this->maxUploadSize;
        $maxImageUploadQuota = (int) $this->uploadQuotaConfiguration->getConfiguration()['max_size_product_image'] * self::MEGABYTE_IN_BYTES;

        if ($maxImageUploadQuota < $maxUploadSize) {
            // if upload limit which is set in BO settings is less than php.ini upload limit, then we check according to that value
            $maxUploadSize = $maxImageUploadQuota;
        }

        if ($maxUploadSize > 0 && $size > $maxUploadSize) {
            throw UploadedImageSizeException::build($maxUploadSize);
        }

        if (!ImageManager::checkImageMemoryLimit($filePath)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }
    }
}
