<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierUpdate;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\CombinationSupplierAssociation;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Associates supplier with product combination
 *
 * @see UpdateCombinationSuppliersHandlerInterface
 */
class UpdateCombinationSuppliersCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var array<int, ProductSupplierUpdate>
     */
    private $combinationSuppliers;

    /**
     * @param int $combinationId
     * @param array<int, array<string, string|int|null>> $combinationSuppliers
     */
    public function __construct(
        int $combinationId,
        array $combinationSuppliers
    ) {
        $this->combinationId = new CombinationId($combinationId);
        $this->setCombinationSuppliers($combinationSuppliers);
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return array<int, ProductSupplierUpdate>
     */
    public function getCombinationSuppliers(): array
    {
        return $this->combinationSuppliers;
    }

    /**
     * @param array<int, array<string, string|int|null>> $productSuppliers
     */
    private function setCombinationSuppliers(array $productSuppliers): void
    {
        if (empty($productSuppliers)) {
            throw new InvalidArgumentException(sprintf(
                'Empty array of combination suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedProductSuppliersCommand::class
            ));
        }

        foreach ($productSuppliers as $productSupplier) {
            $this->combinationSuppliers[] = new ProductSupplierUpdate(
                new CombinationSupplierAssociation(
                    $this->combinationId->getValue(),
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
