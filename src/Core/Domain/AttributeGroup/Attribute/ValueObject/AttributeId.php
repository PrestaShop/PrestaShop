<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;

/**
 * Provides identification data of Attribute
 */
final class AttributeId
{
    /**
     * @var int
     */
    private int $attributeId;

    /**
     * @param int $attributeId
     *
     * @throws AttributeConstraintException
     */
    public function __construct(int $attributeId)
    {
        $this->assertIsIntegerGreaterThanZero($attributeId);
        $this->attributeId = $attributeId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->attributeId;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws AttributeConstraintException
     */
    private function assertIsIntegerGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new AttributeConstraintException(sprintf('Invalid attribute id "%s".', var_export($value, true)), AttributeConstraintException::INVALID_ID);
        }
    }
}
