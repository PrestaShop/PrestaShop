<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
