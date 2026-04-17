<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

/**
 * Deletes product image
 */
class DeleteProductImageCommand
{
    /**
     * @var ImageId
     */
    private $imageId;

    /**
     * @param int $imageId
     */
    public function __construct(int $imageId)
    {
        $this->imageId = new ImageId($imageId);
    }

    /**
     * @return ImageId
     */
    public function getImageId(): ImageId
    {
        return $this->imageId;
    }
}
