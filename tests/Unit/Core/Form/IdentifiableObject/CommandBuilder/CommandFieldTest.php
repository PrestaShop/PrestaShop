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
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandField;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

class CommandFieldTest extends TestCase
{
    /**
     * @dataProvider getValidParameters
     *
     * @param string $commandSetter
     * @param array<int, DataField> $dataFields
     * @param bool $isMultiShopField
     */
    public function testValidConstructors(string $commandSetter, array $dataFields, bool $isMultiShopField): void
    {
        if ($isMultiShopField) {
            $field = CommandField::createAsMultiShop($commandSetter, $dataFields);
        } else {
            $field = CommandField::createAsSingleShop($commandSetter, $dataFields);
        }
        $this->assertInstanceOf(CommandField::class, $field);
        $this->assertSame($commandSetter, $field->getCommandSetter());
        $this->assertSame($dataFields, $field->getDataFields());
        $this->assertSame($isMultiShopField, $field->isMultiShopField());
    }

    public function getValidParameters(): iterable
    {
        $dataFields = [
            new DataField('foo', DataField::TYPE_STRING),
            new DataField('bar', DataField::TYPE_INT, 42),
        ];
        yield 'multishop enabled' => [
            'setMyField',
            $dataFields,
            true,
        ];
        yield 'multishop disabled' => [
            'setMyField',
            $dataFields,
            false,
        ];
    }

    /**
     * @dataProvider getInvalidParameters
     *
     * @param string $commandSetter
     * @param array<int, DataField> $dataFields
     * @param string $expectedException
     */
    public function testInvalidConstructors(string $commandSetter, array $dataFields, string $expectedException): void
    {
        $this->expectException($expectedException);

        CommandField::createAsSingleShop($commandSetter, $dataFields);
    }

    public function getInvalidParameters(): iterable
    {
        yield 'no data fields' => [
            'setMyField',
            [],
            InvalidArgumentException::class,
        ];
        yield 'invalid data field type' => [
            'setMyField',
            [new InvalidArgumentException()],
            InvalidArgumentException::class,
        ];
    }
}
