<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * DTO for product that was found by search
 */
class FoundProduct
{
    /**
     * @var bool
     */
    private $availableOutOfStock;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $taxRate;

    /**
     * @var string
     */
    private $formattedPrice;

    /**
     * @var float
     */
    private $priceTaxIncl;

    /**
     * @var float
     */
    private $priceTaxExcl;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var string
     */
    private $location;

    /**
     * @var ProductCombination[]
     */
    private $combinations;

    /**
     * @var ProductCustomizationField[]
     */
    private $customizationFields;

    private bool $isVirtual;

    /**
     * @param int $productId
     * @param string $name
     * @param string $formattedPrice
     * @param float $priceTaxIncl
     * @param float $priceTaxExcl
     * @param float $taxRate
     * @param int $stock
     * @param string $location
     * @param bool $availableOutOfStock
     * @param ProductCombination[] $combinations
     * @param ProductCustomizationField[] $customizationFields
     * @param bool $isVirtual
     */
    public function __construct(
        int $productId,
        string $name,
        string $formattedPrice,
        float $priceTaxIncl,
        float $priceTaxExcl,
        float $taxRate,
        int $stock,
        string $location,
        bool $availableOutOfStock,
        bool $isVirtual,
        array $combinations = [],
        array $customizationFields = [],
    ) {
        $this->productId = $productId;
        $this->name = $name;
        $this->formattedPrice = $formattedPrice;
        $this->priceTaxIncl = $priceTaxIncl;
        $this->priceTaxExcl = $priceTaxExcl;
        $this->taxRate = $taxRate;
        $this->stock = $stock;
        $this->location = $location;
        $this->availableOutOfStock = $availableOutOfStock;
        $this->combinations = $combinations;
        $this->customizationFields = $customizationFields;
        $this->isVirtual = $isVirtual;
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
    public function getLocation(): string
    {
        return $this->location;
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
    public function getFormattedPrice(): string
    {
        return $this->formattedPrice;
    }

    /**
     * @return float
     */
    public function getPriceTaxIncl(): float
    {
        return $this->priceTaxIncl;
    }

    /**
     * @return float
     */
    public function getPriceTaxExcl(): float
    {
        return $this->priceTaxExcl;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @return ProductCombination[]
     */
    public function getCombinations(): array
    {
        return $this->combinations;
    }

    /**
     * @return ProductCustomizationField[]
     */
    public function getCustomizationFields(): array
    {
        return $this->customizationFields;
    }

    /**
     * @return bool
     */
    public function isAvailableOutOfStock(): bool
    {
        return $this->availableOutOfStock;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }
}
