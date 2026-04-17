<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult;

/**
 * Stores attributes data that's needed for editing.
 */
class EditableAttribute
{
    public function __construct(
        private readonly int $attributeId,
        private readonly int $attributeGroupId,
        private readonly array $localizedNames,
        private readonly string $color,
        private readonly array $shopAssociationIds,
        private readonly ?array $textureImage
    ) {
    }

    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    public function getAttributeGroupId(): int
    {
        return $this->attributeGroupId;
    }

    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->shopAssociationIds;
    }

    /**
     * @return mixed
     */
    public function getTextureImage()
    {
        return $this->textureImage;
    }
}
