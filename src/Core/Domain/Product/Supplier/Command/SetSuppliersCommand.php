<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * This command is used to set (or assign) suppliers to a product It is used for both product with or without
 * combinations and only defines the association not the content themselves. To update contents you need to use
 * UpdateProductSuppliersCommand or UpdateCombinationSuppliersCommand one you have correctly set the associations
 * with this command.
 *
 * When no default supplier was associated this command will automatically use the first provided one, however
 * to change it to your need you can use SetProductDefaultSupplierCommand.
 */
class SetSuppliersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var SupplierId[]
     */
    private $supplierIds;

    /**
     * @param int $productId
     * @param array $supplierIds
     */
    public function __construct(int $productId, array $supplierIds)
    {
        $this->productId = new ProductId($productId);
        $this->setSupplierIds($supplierIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return SupplierId[]
     */
    public function getSupplierIds(): array
    {
        return $this->supplierIds;
    }

    private function setSupplierIds(array $supplierIds): void
    {
        if (empty($supplierIds)) {
            throw new InvalidArgumentException(sprintf(
                'Empty array of suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedProductSuppliersCommand::class
            ));
        }

        $this->supplierIds = [];
        foreach ($supplierIds as $supplierId) {
            $this->supplierIds[] = new SupplierId($supplierId);
        }
    }
}
