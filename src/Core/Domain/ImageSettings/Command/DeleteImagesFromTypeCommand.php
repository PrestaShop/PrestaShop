<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Delete images from defined image type
 */
class DeleteImagesFromTypeCommand
{
    private ImageTypeId $imageTypeId;

    /**
     * @param int $imageTypeId
     */
    public function __construct(int $imageTypeId)
    {
        $this->imageTypeId = new ImageTypeId($imageTypeId);
    }

    /**
     * @return ImageTypeId
     */
    public function getImageTypeId(): ImageTypeId
    {
        return $this->imageTypeId;
    }
}
