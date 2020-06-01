<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    const TYPE_STANDARD = 0;

    /**
     * A pack consists multiple units of product.
     */
    const TYPE_PACK = 1;

    /**
     * Items that are not in physical form and can be sold without requiring any shipping
     * E.g. downloadable photos, videos, software, services etc.
     */
    const TYPE_VIRTUAL = 2;

    /**
     * Product containing combinations of different attributes
     */
    const TYPE_COMBINATION = 3;

    /**
     * A list of available types
     */
    const AVAILABLE_TYPES = [
        self::TYPE_STANDARD,
        self::TYPE_PACK,
        self::TYPE_VIRTUAL,
        self::TYPE_COMBINATION,
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $value)
    {
        $this->assertProductType($value);
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @throws ProductConstraintException
     */
    private function assertProductType(int $value): void
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
