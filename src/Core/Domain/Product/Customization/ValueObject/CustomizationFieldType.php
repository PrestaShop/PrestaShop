<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldConstraintException;

/**
 * Holds value of customization type
 */
class CustomizationFieldType
{
    /**
     * Value representing customization file type
     */
    public const TYPE_FILE = 0;

    /**
     * Value representing customization text type
     */
    public const TYPE_TEXT = 1;

    /**
     * Available customization types
     */
    public const AVAILABLE_TYPES = [
        'file' => self::TYPE_FILE,
        'text' => self::TYPE_TEXT,
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
        $this->assertAvailableType($value);
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
     * @return bool
     */
    public function isTextType(): bool
    {
        return $this->value === self::TYPE_TEXT;
    }

    /**
     * @param int $value
     *
     * @throws CustomizationFieldConstraintException
     */
    private function assertAvailableType(int $value): void
    {
        if (!in_array($value, self::AVAILABLE_TYPES)) {
            throw new CustomizationFieldConstraintException(
                sprintf(
                    'Invalid customization type "%d". Available types are: %d',
                    $value,
                    implode(',', self::AVAILABLE_TYPES)
                ),
                CustomizationFieldConstraintException::INVALID_TYPE
            );
        }
    }
}
