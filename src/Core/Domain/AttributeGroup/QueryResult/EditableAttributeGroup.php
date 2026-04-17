<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Stores attribute groups data that's needed for editing.
 */
class EditableAttributeGroup
{
    /**
     * @var AttributeGroupId
     */
    private $attributeGroupId;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @var array
     */
    private $publicName;

    /**
     * @var string
     */
    private $type;

    /**
     * @param int $attributeGroupId
     * @param string[] $name
     * @param array $publicName
     * @param string $type
     * @param int[] $associatedShopIds
     */
    public function __construct(
        int $attributeGroupId,
        array $name,
        array $publicName,
        string $type,
        array $associatedShopIds
    ) {
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);
        $this->name = $name;
        $this->associatedShopIds = $associatedShopIds;
        $this->publicName = $publicName;
        $this->type = $type;
    }

    /**
     * @return AttributeGroupId
     */
    public function getAttributeGroupId(): AttributeGroupId
    {
        return $this->attributeGroupId;
    }

    /**
     * @return string[]
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    /**
     * @return array
     */
    public function getPublicName(): array
    {
        return $this->publicName;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
