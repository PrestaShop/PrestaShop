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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;

/**
 * Holds value of out of stock type
 */
class OutOfStockType
{
    /**
     * Product is not available for order when out of stock.
     */
    public const OUT_OF_STOCK_NOT_AVAILABLE = 0;

    /**
     * Product is available for order even when out of stock.
     */
    public const OUT_OF_STOCK_AVAILABLE = 1;

    /**
     * Product availability when out of stock is define by shop settings.
     */
    public const OUT_OF_STOCK_DEFAULT = 2;

    public const ALLOWED_OUT_OF_STOCK_TYPES = [
        self::OUT_OF_STOCK_AVAILABLE,
        self::OUT_OF_STOCK_NOT_AVAILABLE,
        self::OUT_OF_STOCK_DEFAULT,
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $outOfStockType
     *
     * @throws ProductStockConstraintException
     */
    public function __construct(int $outOfStockType)
    {
        $this->setOutOfStockType($outOfStockType);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $outOfStockType
     *
     * @throws ProductStockConstraintException
     */
    private function setOutOfStockType(int $outOfStockType): void
    {
        if (!in_array($outOfStockType, self::ALLOWED_OUT_OF_STOCK_TYPES)) {
            throw new ProductStockConstraintException(
                sprintf(
                    'Cannot use product pack stock type %s, allowed values are: %s',
                    $outOfStockType,
                    implode(', ', self::ALLOWED_OUT_OF_STOCK_TYPES)
                ),
                ProductStockConstraintException::INVALID_OUT_OF_STOCK_TYPE
            );
        }

        $this->value = $outOfStockType;
    }
}
