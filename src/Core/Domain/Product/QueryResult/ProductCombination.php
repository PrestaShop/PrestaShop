<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Holds product combination data
 */
class ProductCombination
{
    /**
     * @var int
     */
    private $attributeCombinationId;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var float
     */
    private $priceTaxExcluded;

    /**
     * @var float
     */
    private $priceTaxIncluded;

    /**
     * @var string
     */
    private $formattedPrice;

    /**
     * @param int $attributeCombinationId
     * @param string $attribute
     * @param int $stock
     * @param string $formattedPrice
     * @param float $priceTaxExcluded
     * @param float $priceTaxIncluded
     * @param string $location
     * @param string $reference
     */
    public function __construct(
        int $attributeCombinationId,
        string $attribute,
        int $stock,
        string $formattedPrice,
        float $priceTaxExcluded,
        float $priceTaxIncluded,
        string $location,
        string $reference
    ) {
        $this->attributeCombinationId = $attributeCombinationId;
        $this->attribute = $attribute;
        $this->stock = $stock;
        $this->formattedPrice = $formattedPrice;
        $this->priceTaxExcluded = $priceTaxExcluded;
        $this->priceTaxIncluded = $priceTaxIncluded;
        $this->location = $location;
        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function getAttributeCombinationId(): int
    {
        return $this->attributeCombinationId;
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return float
     */
    public function getPriceTaxExcluded(): float
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return float
     */
    public function getPriceTaxIncluded(): float
    {
        return $this->priceTaxIncluded;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return $this->formattedPrice;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $name
     */
    public function appendAttributeName(string $name)
    {
        $this->attribute .= ' - ' . $name;
    }
}
