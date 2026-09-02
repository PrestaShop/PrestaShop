<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/** One service that uploads all category images */
class CategoryImageUploader
{
    /**
     * @var ImageUploaderInterface
     */
    private $categoryCoverUploader;

    /**
     * @var ImageUploaderInterface
     */
    private $categoryThumbnailUploader;

    public function __construct(
        ImageUploaderInterface $categoryCoverUploader,
        ImageUploaderInterface $categoryThumbnailUploader
    ) {
        $this->categoryCoverUploader = $categoryCoverUploader;
        $this->categoryThumbnailUploader = $categoryThumbnailUploader;
    }

    /**
     * @param CategoryId $categoryId
     * @param UploadedFile|null $coverImage
     * @param UploadedFile|null $thumbnailImage
     */
    public function uploadImages(
        CategoryId $categoryId,
        ?UploadedFile $coverImage = null,
        ?UploadedFile $thumbnailImage = null
    ): void {
        if (null !== $coverImage) {
            $this->categoryCoverUploader->upload($categoryId->getValue(), $coverImage);
        }

        if (null !== $thumbnailImage) {
            $this->categoryThumbnailUploader->upload($categoryId->getValue(), $thumbnailImage);
        }
    }
}
