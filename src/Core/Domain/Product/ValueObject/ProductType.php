<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
