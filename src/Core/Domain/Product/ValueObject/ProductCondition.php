<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds product condition value
 */
class ProductCondition
{
    public const NEW = 'new';
    public const USED = 'used';
    public const REFURBISHED = 'refurbished';
    public const OPEN_BOX = 'open_box';
    public const DAMAGED = 'damaged';
    public const NEW_WITH_DEFECTS = 'new_with_defects';

    /**
     * A list of available values
     */
    public const AVAILABLE_CONDITIONS = [
        self::NEW,
        self::USED,
        self::REFURBISHED,
        self::OPEN_BOX,
        self::DAMAGED,
        self::NEW_WITH_DEFECTS,
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
        $this->assertValueIsAllowed($value);
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
    private function assertValueIsAllowed(string $value): void
    {
        if (!in_array($value, self::AVAILABLE_CONDITIONS, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product condition "%s". Allowed conditions are: "%s"',
                    $value,
                    implode(',', self::AVAILABLE_CONDITIONS)
                ),
                ProductConstraintException::INVALID_CONDITION
            );
        }
    }
}
