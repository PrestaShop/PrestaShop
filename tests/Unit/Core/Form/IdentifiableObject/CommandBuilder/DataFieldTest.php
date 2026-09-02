<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
