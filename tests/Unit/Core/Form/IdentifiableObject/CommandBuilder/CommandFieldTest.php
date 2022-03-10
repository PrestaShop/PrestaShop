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
     * @param callable|null $argumentsUpdater
     * @param bool $isMultiShopField
     */
    public function testValidConstructors(
        string $dataPath,
        string $commandSetter,
        string $type,
        ?callable $argumentsUpdater,
        bool $isMultiShopField
    ): void {
        $field = new CommandField(
            $dataPath,
            $commandSetter,
            $type,
            $argumentsUpdater,
            $isMultiShopField
        );
        $this->assertInstanceOf(CommandField::class, $field);
        $this->assertEquals($dataPath, $field->getDataPath());
        $this->assertSame($commandSetter, $field->getCommandSetter());
        $this->assertSame($type, $field->getType());
        $this->assertSame($argumentsUpdater, $field->getArgumentsUpdater());
        $this->assertSame($isMultiShopField, $field->isMultiShopField());
    }

    public function getValidParameters(): iterable
    {
        yield 'multishop enabled' => [
            '[form_data][my_field]',
            'setMyField',
            CommandField::TYPE_STRING,
            null,
            true,
        ];
        yield 'arguments updater callback' => [
            '[form_data][my_field]',
            'setMyField',
            CommandField::TYPE_STRING,
            static function (): array {
                return [];
            },
            false,
        ];
        yield 'string type' => [
            '[form_data][my_field]',
            'setMyField',
            CommandField::TYPE_STRING,
            null,
            false,
        ];
        yield 'boolean type' => [
            'form_data.my_field',
            'setMyField',
            CommandField::TYPE_BOOL,
            null,
            false,
        ];
        yield 'integer type' => [
            'my_field',
            'setMyField',
            CommandField::TYPE_INT,
            null,
            false,
        ];
        yield 'array type' => [
            'localized_field',
            'setLocalizedField',
            CommandField::TYPE_ARRAY,
            null,
            false,
        ];
        yield 'datetime type' => [
            'date_time',
            'setDate',
            CommandField::TYPE_DATETIME,
            null,
            false,
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
    public function testInvalidConstructors(
        string $dataPath,
        string $commandSetter,
        string $type,
        bool $isMultiShopField,
        string $expectedException
    ): void {
        $this->expectException($expectedException);
        new CommandField(
            $dataPath,
            $commandSetter,
            $type,
            null,
            $isMultiShopField
        );
    }

    public function getInvalidParameters(): iterable
    {
        yield 'invalid type' => [
            '[form_data][my_field]',
            'setMyField',
            'invalid',
            true,
            InvalidCommandFieldTypeException::class,
        ];
        yield 'empty path' => [
            '',
            'setMyField',
            CommandField::TYPE_INT,
            false,
            InvalidPropertyPathException::class,
        ];
        yield 'invalid path' => [
            '[form_data.objectField',
            'setMyField',
            CommandField::TYPE_INT,
            true,
            InvalidPropertyPathException::class,
        ];
    }
}
