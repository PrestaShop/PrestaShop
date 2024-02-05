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

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType;

/**
 * Edits existing attribute group
 */
class EditAttributeGroupCommand
{
    private AttributeGroupId $attributeGroupId;

    /**
     * @var string[]|null
     */
    private ?array $localizedNames;

    /**
     * @var int[]|null
     */
    private ?array $associatedShopIds;

    /**
     * @var string[]|null
     */
    private ?array $localizedPublicNames;

    private ?AttributeGroupType $type;

    public function __construct(int $attributeGroupId)
    {
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);
    }

    /**
     * @return AttributeGroupId
     */
    public function getAttributeGroupId(): AttributeGroupId
    {
        return $this->attributeGroupId;
    }

    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames ?? null;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws AttributeGroupConstraintException
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        $this->assertNamesAreValid(
            $localizedNames,
            'Attribute name cannot be empty',
            AttributeGroupConstraintException::EMPTY_NAME
        );
        $this->localizedNames = $localizedNames;

        return $this;
    }

    public function getLocalizedPublicNames(): ?array
    {
        return $this->localizedPublicNames ?? null;
    }

    /**
     * @param string[] $localizedPublicNames
     *
     * @return $this
     *
     * @throws AttributeGroupConstraintException
     */
    public function setLocalizedPublicNames(array $localizedPublicNames): self
    {
        $this->assertNamesAreValid(
            $localizedPublicNames,
            'Attribute public name cannot be empty',
            AttributeGroupConstraintException::EMPTY_PUBLIC_NAME
        );
        $this->localizedPublicNames = $localizedPublicNames;

        return $this;
    }

    public function getType(): ?AttributeGroupType
    {
        return $this->type ?? null;
    }

    public function setType(string $type): self
    {
        $this->type = new AttributeGroupType($type);

        return $this;
    }

    public function getAssociatedShopIds(): ?array
    {
        return $this->associatedShopIds ?? null;
    }

    /**
     * @param int[] $associatedShopIds
     *
     * @return $this
     */
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
     * @throws AttributeGroupConstraintException
     */
    private function assertNamesAreValid(array $names, string $message, int $errorCode): void
    {
        if (empty($names)) {
            throw new AttributeGroupConstraintException($message, $errorCode);
        }
    }
}
