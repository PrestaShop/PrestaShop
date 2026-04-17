<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Edit existing attribute
 */
class EditAttributeCommand
{
    private AttributeId $attributeId;

    private ?AttributeGroupId $attributeGroupId;

    private ?array $localizedNames;

    private ?string $color;

    private ?string $pathName;

    /**
     * @var int[]
     */
    private ?array $associatedShopIds;

    /**
     * @param int $attributeId
     *
     * @throws AttributeConstraintException
     * @throws AttributeGroupConstraintException
     */
    public function __construct(int $attributeId)
    {
        $this->attributeId = new AttributeId($attributeId);
    }

    /**
     * @return AttributeId
     */
    public function getAttributeId(): AttributeId
    {
        return $this->attributeId;
    }

    public function getAttributeGroupId(): ?AttributeGroupId
    {
        return $this->attributeGroupId ?? null;
    }

    public function setAttributeGroupId(int $attributeGroupId): self
    {
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);

        return $this;
    }

    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames ?? null;
    }

    public function setLocalizedNames(array $localizedNames): self
    {
        $this->assertValuesAreValid($localizedNames);
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color ?? null;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): ?array
    {
        return $this->associatedShopIds ?? null;
    }

    public function setAssociatedShopIds(array $associatedShopIds): self
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
    }

    /**
     * @param string $pathName
     */
    public function setTextureFilePath(
        string $pathName,
    ): void {
        $this->pathName = $pathName;
    }

    public function getTextureFilePath(): ?string
    {
        return $this->pathName ?? null;
    }

    /**
     * Asserts that attribute group's names are valid.
     *
     * @param string[] $names
     *
     * @throws AttributeConstraintException
     */
    private function assertValuesAreValid(array $names): void
    {
        if (empty($names)) {
            throw new AttributeConstraintException('Attribute name cannot be empty', AttributeConstraintException::EMPTY_NAME);
        }
    }
}
