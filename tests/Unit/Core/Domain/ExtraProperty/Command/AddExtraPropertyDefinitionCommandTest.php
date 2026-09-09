<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ExtraProperty\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;

/**
 * The command carries the DEFAULT clause value down to ExtraPropertyDefinition, whose
 * $defaultValue is typed int|float|string|bool|null. The command must accept and preserve
 * the same scalar types instead of coercing everything to string, so a non-string default
 * (e.g. an int coming from the Admin API JSON payload) keeps its type through the CQRS layer.
 */
class AddExtraPropertyDefinitionCommandTest extends TestCase
{
    /**
     * @dataProvider defaultValueProvider
     */
    public function testItPreservesTheScalarTypeOfTheDefaultValue(int|float|string|bool|null $defaultValue): void
    {
        $command = new AddExtraPropertyDefinitionCommand(
            entityName: 'product',
            propertyName: 'internal_code',
            defaultValue: $defaultValue,
        );

        $this->assertSame($defaultValue, $command->getDefaultValue());
    }

    /**
     * @return iterable<string, array{int|float|string|bool|null}>
     */
    public static function defaultValueProvider(): iterable
    {
        yield 'int' => [42];
        yield 'float' => [3.5];
        yield 'bool true' => [true];
        yield 'bool false' => [false];
        yield 'string' => ['some-default'];
        yield 'null' => [null];
    }
}
