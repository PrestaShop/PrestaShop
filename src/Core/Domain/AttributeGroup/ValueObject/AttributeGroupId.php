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

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;

/**
 * Defines Attribute group ID with its constraints.
 */
class AttributeGroupId
{
    /**
     * @var int
     */
    private $attributeGroupId;

    /**
     * @param int $attributeGroupId
     */
    public function __construct(int $attributeGroupId)
    {
        $this->assertIntegerIsGreaterThanZero($attributeGroupId);

        $this->attributeGroupId = $attributeGroupId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->attributeGroupId;
    }

    /**
     * @param int $attributeGroupId
     *
     * @throws AttributeGroupConstraintException
     */
    private function assertIntegerIsGreaterThanZero(int $attributeGroupId): void
    {
        if (0 > $attributeGroupId) {
            throw new AttributeGroupConstraintException(
                sprintf('Invalid attributeGroup id %s supplied. Attribute group id must be positive integer.', $attributeGroupId),
                AttributeGroupConstraintException::INVALID_ID
            );
        }
    }
}
