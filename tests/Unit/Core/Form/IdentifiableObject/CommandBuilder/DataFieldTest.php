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

use DateTime;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataFieldException;
use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;

class DataFieldTest extends TestCase
{
    /**
     * @dataProvider getValidParametersWithoutDefaultValue
     */
    public function testValidConstructorsWithoutDefaultValue(string $path, string $type): void
    {
        $dataField = new DataField($path, $type);

        $this->assertInstanceOf(DataField::class, $dataField);
        $this->assertEquals($path, $dataField->getPropertyPath());
        $this->assertSame($type, $dataField->getType());
        $this->assertFalse($dataField->hasDefaultValue());
    }

    public function getValidParametersWithoutDefaultValue(): iterable
    {
        yield 'array path' => [
            '[child][field]',
            DataField::TYPE_STRING,
        ];
        yield 'object path' => [
            'child.field',
            DataField::TYPE_STRING,
        ];
        yield 'string type' => [
            'field',
            DataField::TYPE_STRING,
        ];
        yield 'boolean type' => [
            'field',
            DataField::TYPE_BOOL,
        ];
        yield 'integer type' => [
            'field',
            DataField::TYPE_INT,
        ];
        yield 'array type' => [
            'field',
            DataField::TYPE_ARRAY,
        ];
        yield 'datetime type' => [
            'field',
            DataField::TYPE_DATETIME,
        ];
    }

    /**
     * @dataProvider getValidParametersWithDefaultValue
     */
    public function testValidConstructorsWithDefaultValue(string $path, string $type, $defaultValue): void
    {
        $dataField = new DataField($path, $type, $defaultValue);

        $this->assertEquals($path, $dataField->getPropertyPath());
        $this->assertSame($type, $dataField->getType());
        $this->assertTrue($dataField->hasDefaultValue());
        $this->assertSame($defaultValue, $dataField->getDefaultValue());
    }

    public function getValidParametersWithDefaultValue(): iterable
    {
        yield 'null default value' => [
            'field',
            DataField::TYPE_STRING,
            null,
        ];
        yield 'unexpected type default value' => [
            'field',
            DataField::TYPE_INT,
            'foo',
        ];
        yield 'array default value' => [
            'field',
            DataField::TYPE_ARRAY,
            ['foo' => 'bar'],
        ];
        yield 'datetime default value' => [
            'field',
            DataField::TYPE_DATETIME,
            new DateTime(),
        ];
    }

    /**
     * @dataProvider getInvalidParameters
     */
    public function testInvalidConstructors(string $path, string $type, string $expectedException): void
    {
        $this->expectException($expectedException);

        new DataField($path, $type);
    }

    public function getInvalidParameters(): iterable
    {
        yield 'empty path' => [
            '',
            DataField::TYPE_INT,
            InvalidPropertyPathException::class,
        ];
        yield 'invalid array path' => [
            '[child][field',
            DataField::TYPE_INT,
            InvalidPropertyPathException::class,
        ];
        yield 'invalid object path start' => [
            '.field',
            DataField::TYPE_INT,
            InvalidPropertyPathException::class,
        ];
        yield 'invalid object path end' => [
            'child.',
            DataField::TYPE_INT,
            InvalidPropertyPathException::class,
        ];
        yield 'invalid type' => [
            'field',
            'invalid',
            DataFieldException::class,
        ];
    }

    public function testMissingDefaultValue(): void
    {
        $dataField = new DataField('field', DataField::TYPE_STRING);

        $this->expectException(DataFieldException::class);

        $dataField->getDefaultValue();
    }
}
