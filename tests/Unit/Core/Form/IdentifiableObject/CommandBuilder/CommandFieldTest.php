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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandField;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\InvalidCommandFieldTypeException;
use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;

class CommandFieldTest extends TestCase
{
    /**
     * @dataProvider getValidParameters
     *
     * @param string $dataPath
     * @param string $commandSetter
     * @param string $type
     * @param bool $isMultiShopField
     */
    public function testValidConstructors(string $dataPath, string $commandSetter, string $type, bool $isMultiShopField): void
    {
        $field = new CommandField($dataPath, $commandSetter, $type, $isMultiShopField);
        $this->assertNotNull($field);
        $this->assertEquals($dataPath, $field->getDataPath());
        $this->assertEquals($commandSetter, $field->getCommandSetter());
        $this->assertEquals($type, $field->getType());
        $this->assertEquals($isMultiShopField, $field->isMultiShopField());
    }

    public function getValidParameters(): iterable
    {
        yield [
            '[form_data][my_field]',
            'setMyField',
            CommandField::TYPE_STRING,
            true,
        ];

        yield [
            'form_data.my_field',
            'setMyField',
            CommandField::TYPE_BOOL,
            false,
        ];

        yield [
            'my_field',
            'setMyField',
            CommandField::TYPE_INT,
            true,
        ];
    }

    /**
     * @dataProvider getInvalidParameters
     *
     * @param string $dataPath
     * @param string $commandSetter
     * @param string $type
     * @param bool $isMultiShopField
     * @param string $expectedException
     */
    public function testInvalidConstructors(string $dataPath, string $commandSetter, string $type, bool $isMultiShopField, string $expectedException): void
    {
        $this->expectException($expectedException);
        new CommandField($dataPath, $commandSetter, $type, $isMultiShopField);
    }

    public function getInvalidParameters(): iterable
    {
        yield [
            '[form_data][my_field]',
            'setMyField',
            'invalid',
            true,
            InvalidCommandFieldTypeException::class,
        ];

        yield [
            '',
            'setMyField',
            CommandField::TYPE_INT,
            false,
            InvalidPropertyPathException::class,
        ];

        yield [
            '[form_data.objectField',
            'setMyField',
            CommandField::TYPE_INT,
            true,
            InvalidPropertyPathException::class,
        ];
    }
}
