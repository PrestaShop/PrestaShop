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

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Profile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads profile logo image
 */
final class ProfileImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * @var string
     */
    private $profileImageDir;

    /**
     * @var string
     */
    private $tmpImageDir;

    /**
     * @param string $profileImageDir
     * @param string $tmpImageDir
     */
    public function __construct(
        string $profileImageDir = _PS_PROFILE_IMG_DIR_,
        string $tmpImageDir = _PS_TMP_IMG_DIR_
    ) {
        $this->profileImageDir = $profileImageDir;
        $this->tmpImageDir = $tmpImageDir;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($profileId, UploadedFile $image)
    {
        $this->checkImageIsAllowedForUpload($image);
        $tempImageName = $this->createTemporaryImage($image);
        $this->deleteOldImage($profileId);

        $destination = $this->profileImageDir . $profileId . '.jpg';
        $this->uploadFromTemp($tempImageName, $destination);
    }

    /**
     * Deletes old image
     *
     * @param int $id
     */
    private function deleteOldImage($id): void
    {
        $profile = new Profile($id);
        $profile->deleteImage();

        $currentImage = $this->tmpImageDir . 'profile_mini_' . $id . '.jpg';

        if (file_exists($currentImage)) {
            unlink($currentImage);
        }
    }
}
