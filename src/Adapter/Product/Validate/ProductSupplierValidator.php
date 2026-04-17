<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use ProductSupplier;

/**
 * Validates ProductSupplier legacy object model
 */
class ProductSupplierValidator extends AbstractObjectModelValidator
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var SupplierRepository
     */
    private $supplierRepository;

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @param ProductRepository $productRepository
     * @param SupplierRepository $supplierProvider
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        SupplierRepository $supplierProvider,
        CurrencyRepository $currencyRepository
    ) {
        $this->productRepository = $productRepository;
        $this->supplierRepository = $supplierProvider;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @param ProductSupplier $productSupplier
     *
     * @throws CoreException
     */
    public function validate(ProductSupplier $productSupplier): void
    {
        $propertiesErrorMap = [
            'product_supplier_reference' => ProductSupplierConstraintException::INVALID_REFERENCE,
            'product_supplier_price_te' => ProductSupplierConstraintException::INVALID_PRICE,
        ];

        foreach ($propertiesErrorMap as $property => $errorCode) {
            $this->validateObjectModelProperty(
                $productSupplier,
                $property,
                ProductSupplierConstraintException::class,
                $errorCode
            );
        }

        $this->assertRelatedEntitiesExists($productSupplier);
    }

    /**
     * @param ProductSupplier $productSupplier
     */
    private function assertRelatedEntitiesExists(ProductSupplier $productSupplier): void
    {
        $this->productRepository->assertProductExists(new ProductId((int) $productSupplier->id_product));
        $this->supplierRepository->assertSupplierExists(new SupplierId((int) $productSupplier->id_supplier));
        $this->currencyRepository->assertCurrencyExists(new CurrencyId((int) $productSupplier->id_currency));
    }
}
