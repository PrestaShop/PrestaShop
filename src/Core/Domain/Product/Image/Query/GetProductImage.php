<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class GetProductImage
{
    /**
     * @var ImageId
     */
    private $imageId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    public function __construct(
        int $imageId,
        ShopConstraint $shopConstraint
    ) {
        $this->imageId = new ImageId($imageId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ImageId
     */
    public function getImageId(): ImageId
    {
        return $this->imageId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
