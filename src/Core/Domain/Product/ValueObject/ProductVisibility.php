<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds value for product visibility setting
 */
class ProductVisibility
{
    public const VISIBLE_IN_CATALOG = 'catalog';
    public const VISIBLE_IN_SEARCH = 'search';
    public const VISIBLE_EVERYWHERE = 'both';
    public const INVISIBLE = 'none';

    public const AVAILABLE_VISIBILITY_VALUES = [
        self::VISIBLE_IN_CATALOG => self::VISIBLE_IN_CATALOG,
        self::VISIBLE_IN_SEARCH => self::VISIBLE_IN_SEARCH,
        self::VISIBLE_EVERYWHERE => self::VISIBLE_EVERYWHERE,
        self::INVISIBLE => self::INVISIBLE,
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->assertIsValidVisibilityValue($value);
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
    private function assertIsValidVisibilityValue(string $value): void
    {
        if (!in_array($value, self::AVAILABLE_VISIBILITY_VALUES, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product visibility "%s". Allowed values are: "%s"',
                    $value,
                    implode(',', self::AVAILABLE_VISIBILITY_VALUES)
                ),
                ProductConstraintException::INVALID_VISIBILITY
            );
        }
    }
}
