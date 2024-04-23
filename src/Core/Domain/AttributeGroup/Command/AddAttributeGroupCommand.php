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
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\InvalidAttributeGroupTypeException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType;

/**
 * Adds new attribute group
 */
class AddAttributeGroupCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @var array
     */
    private $localizedPublicNames;

    /**
     * @var AttributeGroupType
     */
    private $type;

    /**
     * @param string[] $localizedNames
     * @param array $localizedPublicNames
     * @param string $type
     * @param int[] $associatedShopIds
     *
     * @throws AttributeGroupConstraintException
     * @throws InvalidAttributeGroupTypeException
     */
    public function __construct(
        array $localizedNames,
        array $localizedPublicNames,
        string $type,
        array $associatedShopIds,
    ) {
        $this->assertNamesAreValid(
            $localizedNames,
            'Attribute name cannot be empty',
            AttributeGroupConstraintException::EMPTY_NAME
        );
        $this->assertNamesAreValid(
            $localizedPublicNames,
            'Attribute public name cannot be empty',
            AttributeGroupConstraintException::EMPTY_PUBLIC_NAME
        );

        $this->localizedNames = $localizedNames;
        $this->localizedPublicNames = $localizedPublicNames;
        $this->type = new AttributeGroupType($type);
        $this->associatedShopIds = $associatedShopIds;
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
     * @return AttributeGroupType
     */
    public function getType(): AttributeGroupType
    {
        return $this->type;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
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
