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

namespace PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\QueryResult\Attribute;

class AttributeGroup
{
    /**
     * @var int
     */
    private $attributeGroupId;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedPublicNames;

    /**
     * @var string
     */
    private $groupType;

    /**
     * @var bool
     */
    private $isColorGroup;

    /**
     * @var int
     */
    private $position;

    /**
     * @var Attribute[]|null
     */
    private $attributes;

    /**
     * @param int $attributeGroupId
     * @param string[] $localizedNames
     * @param string[] $localizedPublicNames
     * @param string $groupType
     * @param bool $isColorGroup
     * @param int $position
     * @param Attribute[]|null $attributes
     */
    public function __construct(
        int $attributeGroupId,
        array $localizedNames,
        array $localizedPublicNames,
        string $groupType,
        bool $isColorGroup,
        int $position,
        ?array $attributes = null
    ) {
        $this->attributeGroupId = $attributeGroupId;
        $this->localizedNames = $localizedNames;
        $this->localizedPublicNames = $localizedPublicNames;
        $this->groupType = $groupType;
        $this->isColorGroup = $isColorGroup;
        $this->position = $position;
        $this->attributes = $attributes;
    }

    /**
     * @return int
     */
    public function getAttributeGroupId(): int
    {
        return $this->attributeGroupId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return string[]
     */
    public function getLocalizedPublicNames(): array
    {
        return $this->localizedPublicNames;
    }

    /**
     * @return string
     */
    public function getGroupType(): string
    {
        return $this->groupType;
    }

    /**
     * @return bool
     */
    public function isColorGroup(): bool
    {
        return $this->isColorGroup;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Returns list of attributes since it's optional returns null when attributes were
     * not queried. Empty array however means that the group contains no attributes.
     *
     * @return Attribute[]|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }
}
