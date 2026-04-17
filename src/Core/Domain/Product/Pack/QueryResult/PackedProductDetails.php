<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult;

/**
 * Holds packed product data
 */
class PackedProductDetails
{
    /**
     * @var int
     */
    protected $productId;

    /**
     * @var string
     */
    protected $productName;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var int
     */
    protected $combinationId;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $imageUrl;

    /**
     * @param int $productId
     * @param int $quantity
     * @param int $combinationId
     * @param string $productName
     * @param string $reference
     * @param string $imageUrl
     */
    public function __construct(
        int $productId,
        int $quantity,
        int $combinationId,
        string $productName,
        string $reference,
        string $imageUrl
    ) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->combinationId = $combinationId;
        $this->productName = $productName;
        $this->reference = $reference;
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
}
