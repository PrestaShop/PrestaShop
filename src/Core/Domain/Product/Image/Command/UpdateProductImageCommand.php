<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateProductImageCommand
{
    /**
     * @var ImageId
     */
    private $imageId;

    /**
     * @var string|null
     */
    private $filePath;

    /**
     * @var bool|null
     */
    private $isCover;

    /**
     * @var array<int, string>|null
     */
    private $localizedLegends;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    public function __construct(int $imageId, ShopConstraint $shopConstraint)
    {
        $this->imageId = new ImageId($imageId);
        $this->shopConstraint = $shopConstraint;
    }

    public function getImageId(): ImageId
    {
        return $this->imageId;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function isCover(): ?bool
    {
        return $this->isCover;
    }

    public function setIsCover(?bool $isCover): self
    {
        $this->isCover = $isCover;

        return $this;
    }

    /**
     * @return array<int, string>|null
     */
    public function getLocalizedLegends(): ?array
    {
        return $this->localizedLegends;
    }

    /**
     * @param array<int, string>|null $localizedLegends
     *
     * @return self
     */
    public function setLocalizedLegends(?array $localizedLegends): self
    {
        $this->localizedLegends = $localizedLegends;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
