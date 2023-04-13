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

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\InvalidAttributeGroupTypeException;

/**
 * Defines Attribute group type with its constraints.
 */
class AttributeGroupType
{
    public const ATTRIBUTE_GROUP_TYPE_SELECT = 'select';
    public const ATTRIBUTE_GROUP_TYPE_RADIO = 'radio';
    public const ATTRIBUTE_GROUP_TYPE_COLOR = 'color';

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     *
     * @throws InvalidAttributeGroupTypeException
     */
    public function __construct(string $type)
    {
        $this->assertTypeExists($type);

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return void
     *
     * @throws InvalidAttributeGroupTypeException
     */
    private function assertTypeExists(string $type): void
    {
        $types = [static::ATTRIBUTE_GROUP_TYPE_COLOR, static::ATTRIBUTE_GROUP_TYPE_SELECT, static::ATTRIBUTE_GROUP_TYPE_RADIO];
        if (!in_array($type, $types)) {
            throw new InvalidAttributeGroupTypeException(
                sprintf(
                    'Invalid attributeGroup type %s supplied.',
                    $type
                )
            );
        }
    }
}
