<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult;

/**
 * Transfers product image data
 */
class ProductImage
{
    /**
     * @var int
     */
    private $imageId;

    /**
     * @var bool
     */
    private $cover;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $localizedLegends;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @var string
     */
    private $thumbnailUrl;

    /**
     * @var int[]
     */
    private $shopIds;

    /**
     * @param int $imageId
     * @param bool $cover
     * @param int $position
     * @param array $localizedLegends
     * @param string $imageUrl
     * @param string $thumbnailUrl
     * @param int[] $shopIds
     */
    public function __construct(
        int $imageId,
        bool $cover,
        int $position,
        array $localizedLegends,
        string $imageUrl,
        string $thumbnailUrl,
        array $shopIds
    ) {
        $this->imageId = $imageId;
        $this->cover = $cover;
        $this->position = $position;
        $this->localizedLegends = $localizedLegends;
        $this->imageUrl = $imageUrl;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->shopIds = $shopIds;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->imageId;
    }

    /**
     * @return bool
     */
    public function isCover(): bool
    {
        return $this->cover;
    }

    /**
     * @return array
     */
    public function getLocalizedLegends(): array
    {
        return $this->localizedLegends;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    /**
     * @return int[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }
}
