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
    private $shopAssociation;

    /**
     * @param int $attributeGroupId
     * @param array $localizedValue
     * @param string $color
     * @param int[] $shopAssociation
     *
     * @throws AttributeConstraintException
     */
    public function __construct(int $attributeGroupId, array $localizedValue, string $color, array $shopAssociation = [])
    {
        $this->assertValuesAreValid($localizedValue);
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);
        $this->localizedNames = $localizedValue;
        $this->color = $color;
        $this->shopAssociation = $shopAssociation;
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
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
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
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
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
