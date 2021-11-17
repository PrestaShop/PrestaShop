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

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds product type value
 */
class ProductType
{
    /**
     * Standard product
     */
    public const TYPE_STANDARD = 'standard';

    /**
     * A pack consists multiple units of product.
     */
    public const TYPE_PACK = 'pack';

    /**
     * Items that are not in physical form and can be sold without requiring any shipping
     * E.g. downloadable photos, videos, software, services etc.
     */
    public const TYPE_VIRTUAL = 'virtual';

    /**
     * Product containing combinations of different attributes
     */
    public const TYPE_COMBINATIONS = 'combinations';

    /**
     * Product created before 178 or via the legacy page may have empty product type, so it is
     * undefined. To know the product type you can use Product::getDynamicProductType() which
     * computes it based on the existing associations.
     *
     * WARNING: this is not accepted as a valid type for this ValueObject
     */
    public const TYPE_UNDEFINED = '';

    /**
     * A list of available types
     */
    public const AVAILABLE_TYPES = [
        self::TYPE_STANDARD,
        self::TYPE_PACK,
        self::TYPE_VIRTUAL,
        self::TYPE_COMBINATIONS,
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     *
     * @throws ProductConstraintException
     */
    public function __construct(string $value)
    {
        $this->assertProductType($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @todo: DTO containing validation looks strange
     *      Consider adding static factories for each type instead of constructor?
     *
     * @param string $value
     *
     * @throws ProductConstraintException
     */
    private function assertProductType(string $value): void
    {
        if (!in_array($value, self::AVAILABLE_TYPES, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product type %s. Valid types are: [%s]',
                    $value,
                    implode(',', self::AVAILABLE_TYPES)
                ),
                ProductConstraintException::INVALID_PRODUCT_TYPE
            );
        }
    }
}
