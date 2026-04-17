<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;

/**
 * Holds value of pack stock type
 */
class PackStockType
{
    /**
     * Stock type: only based on pack quantity
     */
    public const STOCK_TYPE_PACK_ONLY = 0;

    /**
     * Stock type: only based on products quantity
     */
    public const STOCK_TYPE_PRODUCTS_ONLY = 1;

    /**
     * Stock type: based on products and pack quantity
     */
    public const STOCK_TYPE_BOTH = 2;

    /**
     * Stock type: based on configuration default value
     */
    public const STOCK_TYPE_DEFAULT = 3;

    public const ALLOWED_PACK_STOCK_TYPES = [
        self::STOCK_TYPE_PACK_ONLY,
        self::STOCK_TYPE_PRODUCTS_ONLY,
        self::STOCK_TYPE_BOTH,
        self::STOCK_TYPE_DEFAULT,
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     *
     * @throws ProductPackConstraintException
     */
    public function __construct(int $value)
    {
        $this->setStockType($value);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $stockType
     *
     * @throws ProductPackConstraintException
     */
    private function setStockType(int $stockType): void
    {
        if (!in_array($stockType, self::ALLOWED_PACK_STOCK_TYPES)) {
            throw new ProductPackConstraintException(
                sprintf(
                    'Cannot use product pack stock type %s, allowed values are: %s',
                    $stockType,
                    implode(', ', self::ALLOWED_PACK_STOCK_TYPES)
                ),
                ProductPackConstraintException::INVALID_STOCK_TYPE
            );
        }

        $this->value = $stockType;
    }
}
