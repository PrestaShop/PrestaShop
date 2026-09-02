<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds value of additional delivery time notes type
 */
class DeliveryTimeNoteType
{
    /**
     * Represents case when additional delivery time note is not used
     */
    public const TYPE_NONE = 0;

    /**
     * Represents case when additional delivery time notes should be taken from default settings
     */
    public const TYPE_DEFAULT = 1;

    /**
     * Represents case when specific additional delivery time notes should be used
     */
    public const TYPE_SPECIFIC = 2;

    /**
     * A list of allowed type values
     */
    public const ALLOWED_TYPES = [
        'none' => self::TYPE_NONE,
        'default' => self::TYPE_DEFAULT,
        'specific' => self::TYPE_SPECIFIC,
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->assertTypeValueIsValid($value);
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
     * @param int $type
     *
     * @throws ProductConstraintException
     */
    private function assertTypeValueIsValid(int $type): void
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid type value of delivery time notes. Got "%d", allowed values are: %s',
                    $type,
                    implode(',', self::ALLOWED_TYPES)
                ),
                ProductConstraintException::INVALID_ADDITIONAL_TIME_NOTES_TYPE
            );
        }
    }
}
