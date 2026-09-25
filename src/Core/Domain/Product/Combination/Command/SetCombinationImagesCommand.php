<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class SetCombinationImagesCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var ImageId[]
     */
    private $imageIds;

    /**
     * @param int $combinationId
     * @param array $imageIds
     */
    /**
     * @var ShopConstraint|null
     */
    private $shopConstraint;

    public function __construct(int $combinationId, array $imageIds, ?ShopConstraint $shopConstraint = null)
    {
        $this->shopConstraint = $shopConstraint;
        $this->combinationId = new CombinationId($combinationId);
        $this->imageIds = array_map(function (int $imageId) { return new ImageId($imageId); }, $imageIds);
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return ImageId[]
     */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }
}
