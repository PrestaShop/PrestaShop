<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Transfers related product data
 */
class RelatedProduct
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @param int $productId
     * @param string $name
     * @param string $reference
     * @param string $imageUrl
     */
    public function __construct(
        int $productId,
        string $name,
        string $reference,
        string $imageUrl
    ) {
        $this->productId = $productId;
        $this->name = $name;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
