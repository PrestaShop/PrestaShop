<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ExtraProperty\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\EditableExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;

/**
 * The edit DTO mirrors ExtraPropertyDefinition, whose $defaultValue is int|float|string|bool|null.
 * It must keep that scalar type instead of narrowing it to string, so the value read back from an
 * INT/BOOL/FLOAT column is not silently stringified before the form layer decides how to render it.
 */
class EditableExtraPropertyDefinitionTest extends TestCase
{
    /**
     * @dataProvider defaultValueProvider
     */
    public function testItPreservesTheScalarTypeOfTheDefaultValue(int|float|string|bool|null $defaultValue): void
    {
        $definition = $this->makeDefinition($defaultValue);

        $this->assertSame($defaultValue, $definition->getDefaultValue());
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

    private function makeDefinition(int|float|string|bool|null $defaultValue): EditableExtraPropertyDefinition
    {
        return new EditableExtraPropertyDefinition(
            id: 1,
            entityName: 'product',
            moduleName: null,
            propertyName: 'internal_code',
            fieldType: ExtraPropertyType::INT,
            fieldScope: ExtraPropertyScope::COMMON,
            sqlIndex: ExtraPropertySqlIndex::NONE,
            nullable: true,
            size: null,
            defaultValue: $defaultValue,
            enumValues: null,
            displayFront: false,
            required: false,
            labelWording: null,
            labelDomain: null,
            descriptionWording: null,
            descriptionDomain: null,
            constraints: null,
            formType: null,
            formOptions: null,
            associatedForms: null,
            associatedGrids: null,
            associatedApis: null,
        );
    }
}
