<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

/**
 * Transfers combination data for editing
 */
class CombinationForEditing
{
    /**
     * @var int
     */
    private $combinationId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var CombinationDetails
     */
    private $details;

    /**
     * @var CombinationPrices
     */
    private $prices;

    /**
     * @var CombinationStock
     */
    private $stock;

    /**
     * @var int[]
     */
    private $imageIds;

    /**
     * @var string
     */
    private $coverThumbnailUrl;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * @param int $combinationId
     * @param int $productId
     * @param string $name
     * @param CombinationDetails $options
     * @param CombinationPrices $prices
     * @param CombinationStock $stock
     * @param int[] $imageIds
     * @param string $coverThumbnailUrl
     * @param bool $isDefault
     */
    public function __construct(
        int $combinationId,
        int $productId,
        string $name,
        CombinationDetails $options,
        CombinationPrices $prices,
        CombinationStock $stock,
        array $imageIds,
        string $coverThumbnailUrl,
        bool $isDefault
    ) {
        $this->combinationId = $combinationId;
        $this->productId = $productId;
        $this->name = $name;
        $this->details = $options;
        $this->stock = $stock;
        $this->prices = $prices;
        $this->imageIds = $imageIds;
        $this->coverThumbnailUrl = $coverThumbnailUrl;
        $this->isDefault = $isDefault;
    }

    /**
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CombinationDetails
     */
    public function getDetails(): CombinationDetails
    {
        return $this->details;
    }

    /**
     * @return CombinationPrices
     */
    public function getPrices(): CombinationPrices
    {
        return $this->prices;
    }

    /**
     * @return CombinationStock
     */
    public function getStock(): CombinationStock
    {
        return $this->stock;
    }

    /**
     * @return int[]
     */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    /**
     * @return string
     */
    public function getCoverThumbnailUrl(): string
    {
        return $this->coverThumbnailUrl;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
