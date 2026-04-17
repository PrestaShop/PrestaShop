<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Adds new attribute
 */
class AddAttributeCommand
{
    /**
     * @var AttributeGroupId
     */
    private $attributeGroupId;

    /**
     * @var array
     */
    private $localizedNames;

    /**
     * @var string
     */
    private $color;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @var string|null
     */
    private $pathName;

    /**
     * @param array $localizedNames
     * @param int[] $associatedShopIds
     *
     * @throws AttributeConstraintException
     */
    public function __construct(int $attributeGroupId, array $localizedNames, string $color, array $associatedShopIds = [])
    {
        $this->assertValuesAreValid($localizedNames);
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);
        $this->localizedNames = $localizedNames;
        $this->color = $color;
        $this->associatedShopIds = $associatedShopIds;
    }

    public function getAttributeGroupId(): AttributeGroupId
    {
        return $this->attributeGroupId;
    }

    /**
     * @return array
     */
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
        return $this->associatedShopIds;
    }

    public function setTextureFilePath(
        string $pathName,
    ): void {
        $this->pathName = $pathName;
    }

    public function getTextureFilePath(): ?string
    {
        return $this->pathName;
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
