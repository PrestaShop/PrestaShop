<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination;

/**
 * Transfers data of single combination attributes
 */
class CombinationAttributeInformation
{
    /**
     * @var int
     */
    private $attributeGroupId;

    /**
     * @var string
     */
    private $attributeGroupName;

    /**
     * @var int
     */
    private $attributeId;

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @param int $attributeGroupId
     * @param string $attributeGroupName
     * @param int $attributeId
     * @param string $attributeName
     */
    public function __construct(
        int $attributeGroupId,
        string $attributeGroupName,
        int $attributeId,
        string $attributeName
    ) {
        $this->attributeGroupId = $attributeGroupId;
        $this->attributeGroupName = $attributeGroupName;
        $this->attributeId = $attributeId;
        $this->attributeName = $attributeName;
    }

    /**
     * @return int
     */
    public function getAttributeGroupId(): int
    {
        return $this->attributeGroupId;
    }

    /**
     * @return string
     */
    public function getAttributeGroupName(): string
    {
        return $this->attributeGroupName;
    }

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    /**
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attributeName;
    }
}
