<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use StockAvailable;

/**
 * Validates StockAvailable legacy object model
 */
class StockAvailableValidator extends AbstractObjectModelValidator
{
    /**
     * @param StockAvailable $stockAvailable
     *
     * @throws CoreException
     */
    public function validate(StockAvailable $stockAvailable): void
    {
        $this->validateStockAvailableProperty($stockAvailable, 'quantity', ProductStockConstraintException::INVALID_QUANTITY);
        $this->validateStockAvailableProperty($stockAvailable, 'location', ProductStockConstraintException::INVALID_LOCATION);
        $this->validateStockAvailableProperty($stockAvailable, 'out_of_stock', ProductStockConstraintException::INVALID_OUT_OF_STOCK);
        $this->validateStockAvailableProperty($stockAvailable, 'id_product');
        $this->validateStockAvailableProperty($stockAvailable, 'id_product_attribute');
        $this->validateStockAvailableProperty($stockAvailable, 'id_shop');
        $this->validateStockAvailableProperty($stockAvailable, 'id_shop_group');
    }

    /**
     * @param StockAvailable $stockAvailable
     * @param string $property
     * @param int $errorCode
     *
     * @throws CoreException
     */
    private function validateStockAvailableProperty(StockAvailable $stockAvailable, string $property, int $errorCode = 0): void
    {
        $this->validateObjectModelProperty(
            $stockAvailable,
            $property,
            ProductStockConstraintException::class,
            $errorCode
        );
    }
}
