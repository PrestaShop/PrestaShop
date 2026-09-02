<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
