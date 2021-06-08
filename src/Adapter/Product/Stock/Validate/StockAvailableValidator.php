<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        $this->validateStockAvailableProperty($stockAvailable, 'depends_on_stock');
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
