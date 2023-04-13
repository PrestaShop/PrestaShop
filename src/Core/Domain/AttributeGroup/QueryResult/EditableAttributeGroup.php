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
     * @param AttributeGroupId $attributeGroupId
     * @param string[] $name
     * @param array $publicName
     * @param string $type
     * @param int[] $associatedShopIds
     */
    public function __construct(
        AttributeGroupId $attributeGroupId,
        array $name,
        array $publicName,
        string $type,
        array $associatedShopIds
    ) {
        $this->attributeGroupId = $attributeGroupId;
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
