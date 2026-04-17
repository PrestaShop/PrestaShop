<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierUpdate;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Updates product suppliers
 */
class UpdateProductSuppliersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductSupplierUpdate[]
     */
    private $productSuppliers;

    /**
     * @param int $productId
     * @param array<int, array<string, mixed>> $productSuppliers
     *
     * @see UpdateProductSuppliersCommand::setProductSuppliers() for $productSuppliers structure
     */
    public function __construct(int $productId, array $productSuppliers)
    {
        $this->setProductSuppliers($productSuppliers, $productId);
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductSupplierUpdate[]
     */
    public function getProductSuppliers(): array
    {
        return $this->productSuppliers;
    }

    /**
     * @param array<int, array<string, mixed>> $productSuppliers
     * @param int $productId
     */
    private function setProductSuppliers(array $productSuppliers, int $productId): void
    {
        if (empty($productSuppliers)) {
            throw new InvalidArgumentException(sprintf(
                'Empty array of product suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedProductSuppliersCommand::class
            ));
        }

        foreach ($productSuppliers as $productSupplier) {
            $this->productSuppliers[] = new ProductSupplierUpdate(
                new ProductSupplierAssociation(
                    $productId,
                    NoCombinationId::NO_COMBINATION_ID,
                    $productSupplier['supplier_id'],
                    !empty($productSupplier['product_supplier_id']) ? $productSupplier['product_supplier_id'] : null
                ),
                $productSupplier['currency_id'],
                $productSupplier['reference'],
                $productSupplier['price_tax_excluded']
            );
        }
    }
}
