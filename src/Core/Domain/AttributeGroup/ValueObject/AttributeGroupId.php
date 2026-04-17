<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;

/**
 * Defines Attribute group ID with its constraints.
 */
class AttributeGroupId
{
    /**
     * @var int
     */
    private $attributeGroupId;

    /**
     * @param int $attributeGroupId
     */
    public function __construct(int $attributeGroupId)
    {
        $this->assertIntegerIsGreaterThanZero($attributeGroupId);

        $this->attributeGroupId = $attributeGroupId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->attributeGroupId;
    }

    /**
     * @param int $attributeGroupId
     *
     * @throws AttributeGroupConstraintException
     */
    private function assertIntegerIsGreaterThanZero(int $attributeGroupId): void
    {
        if (0 >= $attributeGroupId) {
            throw new AttributeGroupConstraintException(
                sprintf('Invalid attributeGroup id %s supplied. Attribute group ID must be a positive integer.', $attributeGroupId),
                AttributeGroupConstraintException::INVALID_ID
            );
        }
    }
}
