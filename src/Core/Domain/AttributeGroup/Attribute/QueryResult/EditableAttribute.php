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

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Stores attributes data that's needed for editing.
 */
class EditableAttribute
{
    /**
     * @var AttributeId
     */
    private $attributeId;

    /**
     * @var AttributeGroupId
     */
    private $attributeGroupId;

    /**
     * @var array
     */
    private $value;

    /**
     * @var string
     */
    private $color;

    /**
     * @var int[]
     */
    private $shopAssociationIds;

    /**
     * @param AttributeId $attributeId
     * @param AttributeGroupId $attributeGroupId
     * @param array $value
     * @param string $color
     * @param int[] $shopAssociationIds
     */
    public function __construct(
        AttributeId $attributeId,
        AttributeGroupId $attributeGroupId,
        array $value,
        string $color,
        array $shopAssociationIds
    ) {
        $this->attributeId = $attributeId;
        $this->attributeGroupId = $attributeGroupId;
        $this->value = $value;
        $this->color = $color;
        $this->shopAssociationIds = $shopAssociationIds;
    }

    /**
     * @return AttributeId
     */
    public function getAttributeId(): AttributeId
    {
        return $this->attributeId;
    }

    /**
     * @return AttributeGroupId
     */
    public function getAttributeGroupId(): AttributeGroupId
    {
        return $this->attributeGroupId;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return int[]
     */
    public function getShopAssociationIds(): array
    {
        return $this->shopAssociationIds;
    }
}
