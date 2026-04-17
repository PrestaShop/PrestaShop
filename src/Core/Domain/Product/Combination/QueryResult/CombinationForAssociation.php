<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

class CombinationForAssociation
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $combinationId;

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
     * @param int $combinationId
     * @param string $name
     * @param string $reference
     * @param string $imageUrl
     */
    public function __construct(int $productId, int $combinationId, string $name, string $reference, string $imageUrl)
    {
        $this->productId = $productId;
        $this->combinationId = $combinationId;
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
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
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
