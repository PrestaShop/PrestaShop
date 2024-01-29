<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

    private ?array $localizedValue;

    private ?string $color;

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
        return $this->attributeGroupId;
    }

    public function setAttributeGroupId(int $attributeGroupId): self
    {
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);

        return $this;
    }

    public function getLocalizedValue(): ?array
    {
        return $this->localizedValue;
    }

    public function setLocalizedValue(array $localizedValue): self
    {
        $this->assertValuesAreValid($localizedValue);
        $this->localizedValue = $localizedValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
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
        return $this->associatedShopIds;
    }

    public function setAssociatedShopIds(array $associatedShopIds): self
    {
        $this->associatedShopIds = $associatedShopIds;

        return $this;
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
