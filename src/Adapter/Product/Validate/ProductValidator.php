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

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

/**
 * Validates product field using legacy object model
 */
class ProductValidator extends AbstractObjectModelValidator
{
    /**
     * Validates Product object model properties using legacy validation
     *
     * @param Product $product
     *
     * @throws ProductConstraintException
     * @throws ProductException
     */
    public function validate(Product $product): void
    {
        $this->validateCustomizability($product);
        $this->validateBasicInfo($product);
        $this->validateOptions($product);
        //@todo; more properties when refactoring other handlers to use updater/validator
    }

    /**
     * @param Product $product
     *
     * @throws ProductConstraintException
     */
    private function validateCustomizability(Product $product): void
    {
        $this->validateObjectModelProperty($product, 'customizable', ProductConstraintException::class);
        $this->validateObjectModelProperty($product, 'text_fields', ProductConstraintException::class);
        $this->validateObjectModelProperty($product, 'uploadable_files', ProductConstraintException::class);
    }

    /**
     * @param Product $product
     *
     * @throws ProductConstraintException
     */
    private function validateBasicInfo(Product $product): void
    {
        $this->validateObjectModelLocalizedProperty(
            $product,
            'name',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_NAME
        );
        $this->validateObjectModelLocalizedProperty(
            $product,
            'description',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_DESCRIPTION
        );
        $this->validateObjectModelLocalizedProperty(
            $product,
            'description_short',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SHORT_DESCRIPTION
        );
    }

    /**
     * @param Product $product
     *
     * @throws CoreException
     */
    private function validateOptions(Product $product): void
    {
        $this->validateObjectModelProperty(
            $product,
            'available_for_order',
            ProductConstraintException::class
        );
        $this->validateObjectModelProperty(
            $product,
            'online_only',
            ProductConstraintException::class
        );
        $this->validateObjectModelProperty(
            $product,
            'show_price',
            ProductConstraintException::class
        );
        $this->validateObjectModelProperty(
            $product,
            'id_manufacturer',
            ProductConstraintException::class
        );
        $this->validateObjectModelProperty(
            $product,
            'visibility',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_VISIBILITY
        );
        $this->validateObjectModelProperty(
            $product,
            'condition',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_CONDITION
        );
        $this->validateObjectModelProperty(
            $product,
            'ean13',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_EAN_13
        );
        $this->validateObjectModelProperty(
            $product,
            'isbn',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_ISBN
        );
        $this->validateObjectModelProperty(
            $product,
            'mpn',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_MPN
        );
        $this->validateObjectModelProperty(
            $product,
            'reference',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_REFERENCE
        );
        $this->validateObjectModelProperty(
            $product,
            'upc',
            ProductConstraintException::class,
            ProductConstraintException::INVALID_UPC
        );
    }
}
