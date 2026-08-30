<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Grid\ExtraPropertiesGridQueryBuilderModifier;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyValueCaster;

/**
 * Covers ExtraPropertiesGridQueryBuilderModifier::castExtraProperties() (post-fetch grid cast)
 * and the nullable-aware NULL handling of ExtraPropertyValueCaster.
 */
class ExtraPropertiesGridCastTest extends TestCase
{
    private const GRID_ID = 'product';

    public function testGridRecordsAreCastToDeclaredTypes(): void
    {
        $boolDef = $this->definition('is_dangerous', ExtraPropertyType::BOOL, nullable: false);
        $intDef = $this->definition('stock_alert', ExtraPropertyType::INT, nullable: true);
        $dateDef = $this->definition('date_last_seen', ExtraPropertyType::DATE, nullable: true);
        $stringDef = $this->definition('packaging_type', ExtraPropertyType::STRING, nullable: true);

        $modifier = $this->buildModifier($boolDef, $intDef, $dateDef, $stringDef);

        $records = $modifier->castExtraProperties([
            [
                'id_product' => '7',
                $boolDef->getFieldName() => '1',
                $intDef->getFieldName() => '42',
                $dateDef->getFieldName() => '2026-06-11 10:00:00',
                $stringDef->getFieldName() => 'box',
            ],
            [
                'id_product' => '8',
                $boolDef->getFieldName() => null,   // NOT NULL bool, missing row → false
                $intDef->getFieldName() => null,    // nullable int → stays null
                $dateDef->getFieldName() => null,
                $stringDef->getFieldName() => null,
            ],
        ], self::GRID_ID);

        $this->assertTrue($records[0][$boolDef->getFieldName()]);
        $this->assertSame(42, $records[0][$intDef->getFieldName()]);
        $this->assertSame('2026-06-11 10:00:00', $records[0][$dateDef->getFieldName()]);
        $this->assertSame('box', $records[0][$stringDef->getFieldName()]);
        // Non-extra columns are untouched.
        $this->assertSame('7', $records[0]['id_product']);

        $this->assertFalse($records[1][$boolDef->getFieldName()]);
        $this->assertNull($records[1][$intDef->getFieldName()]);
        $this->assertNull($records[1][$dateDef->getFieldName()]);
        $this->assertNull($records[1][$stringDef->getFieldName()]);
    }

    public function testRecordsUntouchedWhenGridHasNoDefinitions(): void
    {
        $modifier = $this->buildModifier($this->definition('is_dangerous', ExtraPropertyType::BOOL, nullable: false));

        $records = [['id_category' => '3', 'name' => 'Roots']];

        $this->assertSame($records, $modifier->castExtraProperties($records, 'category'));
    }

    public function testMissingExtraColumnInRecordIsIgnored(): void
    {
        $boolDef = $this->definition('is_dangerous', ExtraPropertyType::BOOL, nullable: false);
        $modifier = $this->buildModifier($boolDef);

        $records = [['id_product' => '7']];

        $this->assertSame($records, $modifier->castExtraProperties($records, self::GRID_ID));
    }

    public function testNullableAwareScalarCastKeepsNullForEveryType(): void
    {
        foreach (ExtraPropertyType::cases() as $type) {
            $this->assertNull(
                ExtraPropertyValueCaster::castFromDb($type, null, true),
                sprintf('NULL must be preserved for nullable %s fields', $type->value)
            );
        }

        // NOT NULL semantics keep the historical coercion.
        $this->assertFalse(ExtraPropertyValueCaster::castFromDb(ExtraPropertyType::BOOL, null, false));
        $this->assertNull(ExtraPropertyValueCaster::castFromDb(ExtraPropertyType::INT, null, false));
    }

    private function buildModifier(ExtraPropertyDefinition ...$definitions): ExtraPropertiesGridQueryBuilderModifier
    {
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection($definitions));

        return new ExtraPropertiesGridQueryBuilderModifier(
            $repository,
            'ps_',
            $this->createMock(LanguageContext::class),
            $this->createMock(ShopContext::class)
        );
    }

    private function definition(string $propertyName, ExtraPropertyType $type, bool $nullable): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            type: $type,
            moduleName: 'demoextrafield',
            nullable: $nullable,
            associatedGrids: [self::GRID_ID],
            labelWording: 'Label',
            labelDomain: 'Modules.Demoextrafield.Admin',
        );
    }
}
