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

namespace PrestaShop\PrestaShop\Adapter\Product\Validate;

use Pack;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

/**
 * Validates product field using legacy object model
 */
class ProductValidator extends AbstractObjectModelValidator
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * This method is specific for product creation only.
     *
     * @param Product $product
     *
     * @throws CoreException
     */
    public function validateCreation(Product $product): void
    {
        $this->validateProductLocalizedProperty(
            $product,
            'name',
            ProductConstraintException::INVALID_NAME
        );
    }

    /**
     * Validates Product object model properties using legacy validation
     *
     * @param Product $product
     *
     * @throws CoreException
     * @throws ProductConstraintException
     * @throws ProductPackConstraintException
     * @throws ProductStockConstraintException
     */
    public function validate(Product $product): void
    {
        $this->validateCustomizability($product);
        $this->validateBasicInfo($product);
        $this->validateOptions($product);
        $this->validateShipping($product);
        $this->validateStock($product);
        //@todo; more properties when refactoring other handlers to use updater/validator
    }

    /**
     * @param Product $product
     *
     * @throws ProductConstraintException
     */
    private function validateCustomizability(Product $product): void
    {
        $this->validateProductProperty($product, 'customizable');
        $this->validateProductProperty($product, 'text_fields');
        $this->validateProductProperty($product, 'uploadable_files');
    }

    /**
     * @param Product $product
     *
     * @throws ProductConstraintException
     */
    private function validateBasicInfo(Product $product): void
    {
        $this->validateProductLocalizedProperty($product, 'name', ProductConstraintException::INVALID_NAME);
        $this->validateProductLocalizedProperty($product, 'description', ProductConstraintException::INVALID_DESCRIPTION);
        $this->validateProductLocalizedProperty($product, 'description_short', ProductConstraintException::INVALID_SHORT_DESCRIPTION);
    }

    /**
     * @param Product $product
     *
     * @throws CoreException
     */
    private function validateOptions(Product $product): void
    {
        $this->validateProductProperty($product, 'available_for_order');
        $this->validateProductProperty($product, 'online_only');
        $this->validateProductProperty($product, 'show_price');
        $this->validateProductProperty($product, 'id_manufacturer');
        $this->validateProductProperty($product, 'visibility', ProductConstraintException::INVALID_VISIBILITY);
        $this->validateProductProperty($product, 'condition', ProductConstraintException::INVALID_CONDITION);
        $this->validateProductProperty($product, 'ean13', ProductConstraintException::INVALID_EAN_13);
        $this->validateProductProperty($product, 'isbn', ProductConstraintException::INVALID_ISBN);
        $this->validateProductProperty($product, 'mpn', ProductConstraintException::INVALID_MPN);
        $this->validateProductProperty($product, 'reference', ProductConstraintException::INVALID_REFERENCE);
        $this->validateProductProperty($product, 'upc', ProductConstraintException::INVALID_UPC);
    }

    /**
     * @param Product $product
     *
     * @throws ProductConstraintException
     */
    private function validateShipping(Product $product): void
    {
        $this->validateProductProperty($product, 'width', ProductConstraintException::INVALID_WIDTH);
        $this->validateProductProperty($product, 'height', ProductConstraintException::INVALID_HEIGHT);
        $this->validateProductProperty($product, 'depth', ProductConstraintException::INVALID_DEPTH);
        $this->validateProductProperty($product, 'weight', ProductConstraintException::INVALID_WEIGHT);
        $this->validateProductProperty($product, 'additional_shipping_cost', ProductConstraintException::INVALID_ADDITIONAL_SHIPPING_COST);
        $this->validateProductProperty($product, 'additional_delivery_times');
        $this->validateProductLocalizedProperty($product, 'delivery_in_stock', ProductConstraintException::INVALID_DELIVERY_TIME_IN_STOCK_NOTES);
        $this->validateProductLocalizedProperty($product, 'delivery_out_stock', ProductConstraintException::INVALID_DELIVERY_TIME_OUT_OF_STOCK_NOTES);
    }

    /**
     * @param Product $product
     *
     * @throws ProductConstraintException
     * @throws ProductPackConstraintException
     * @throws ProductStockConstraintException
     */
    private function validateStock(Product $product): void
    {
        $advancedStockEnabled = (bool) $this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT');
        if ($advancedStockEnabled) {
            $this->validateAdvancedStock($product);
        } else {
            $this->validateClassicStock($product);
        }

        $this->validateProductProperty($product, 'low_stock_threshold');
        $this->validateProductProperty($product, 'low_stock_alert');
        $this->validateProductProperty($product, 'available_date');
        $this->validateProductProperty($product, 'minimal_quantity', ProductConstraintException::INVALID_MINIMAL_QUANTITY);
        $this->validateProductProperty($product, 'location', ProductConstraintException::INVALID_LOCATION);
        $this->validateProductLocalizedProperty($product, 'available_later', ProductConstraintException::INVALID_AVAILABLE_LATER);
        $this->validateProductLocalizedProperty($product, 'available_now', ProductConstraintException::INVALID_AVAILABLE_NOW);
    }

    /**
     * @param Product $product
     *
     * @throws ProductStockConstraintException
     */
    private function validateClassicStock(Product $product): void
    {
        // Depends on stock is only available in advanced mode
        if ((bool) $product->depends_on_stock) {
            throw new ProductStockConstraintException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }

        if ((bool) $product->advanced_stock_management) {
            throw new ProductStockConstraintException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }
    }

    /**
     * @param Product $product
     *
     * @throws ProductPackConstraintException
     * @throws ProductStockConstraintException
     */
    private function validateAdvancedStock(Product $product): void
    {
        if ((bool) $product->depends_on_stock && !(bool) $product->advanced_stock_management) {
            throw new ProductStockConstraintException(
                'You cannot perform this action when advanced_stock_management is disabled on the product',
                ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_PRODUCT_DISABLED
            );
        }

        $this->checkPackStockType($product);
    }

    /**
     * @param Product $product
     *
     * @throws ProductPackConstraintException
     */
    private function checkPackStockType(Product $product): void
    {
        // If the product doesn't depend on stock or is not a Pack no problem
        if (!$product->depends_on_stock || !Pack::isPack($product->id)) {
            return;
        }

        // Get pack stock type (or default configuration if needed)
        $packStockType = $product->pack_stock_type;
        if ($packStockType === Pack::STOCK_TYPE_DEFAULT) {
            $packStockType = (int) $this->configuration->get('PS_PACK_STOCK_TYPE');
        }

        // Either the pack has its own stock, or else ALL products from the pack must depend on the stock as well
        if ($packStockType === Pack::STOCK_TYPE_PACK_ONLY || Pack::allUsesAdvancedStockManagement($product->id)) {
            return;
        }

        throw new ProductPackConstraintException(
            'You cannot link your pack to product stock because one of them has no advanced stock enabled',
            ProductPackConstraintException::INCOMPATIBLE_STOCK_TYPE
        );
    }

    /**
     * @param Product $product
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws ProductConstraintException
     */
    private function validateProductProperty(Product $product, string $propertyName, int $errorCode = 0): void
    {
        $this->validateObjectModelProperty(
            $product,
            $propertyName,
            ProductConstraintException::class,
            $errorCode
        );
    }

    /**
     * @param Product $product
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws ProductConstraintException
     */
    private function validateProductLocalizedProperty(Product $product, string $propertyName, int $errorCode = 0): void
    {
        $this->validateObjectModelLocalizedProperty(
            $product,
            $propertyName,
            ProductConstraintException::class,
            $errorCode
        );
    }
}
