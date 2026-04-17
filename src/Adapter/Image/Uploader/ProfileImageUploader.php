<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
