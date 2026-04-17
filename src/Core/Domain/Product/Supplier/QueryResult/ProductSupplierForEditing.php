<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult;

/**
 * Transfers product supplier for editing data
 */
class ProductSupplierForEditing
{
    /**
     * @var int
     */
    private $productSupplierId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $supplierId;

    /**
     * @var string
     */
    private $supplierName;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $priceTaxExcluded;

    /**
     * @var int
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $combinationId;

    /**
     * @param int $productSupplierId ProductSupplier entity record id
     * @param int $productId the associated product id
     * @param int $supplierId the associated supplier id
     * @param string $reference the reference for this product supplier
     * @param string $priceTaxExcluded
     * @param int $currencyId
     * @param int|null $combinationId
     */
    public function __construct(
        int $productSupplierId,
        int $productId,
        int $supplierId,
        string $supplierName,
        string $reference,
        string $priceTaxExcluded,
        int $currencyId,
        ?int $combinationId = null
    ) {
        $this->productSupplierId = $productSupplierId;
        $this->productId = $productId;
        $this->supplierId = $supplierId;
        $this->supplierName = $supplierName;
        $this->reference = $reference;
        $this->priceTaxExcluded = $priceTaxExcluded;
        $this->currencyId = $currencyId;
        $this->combinationId = $combinationId;
    }

    /**
     * @return int
     */
    public function getProductSupplierId(): int
    {
        return $this->productSupplierId;
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
    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    /**
     * @return string
     */
    public function getSupplierName(): string
    {
        return $this->supplierName;
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
    public function getPriceTaxExcluded(): string
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @return int|null
     */
    public function getCombinationId(): ?int
    {
        return $this->combinationId;
    }
}
