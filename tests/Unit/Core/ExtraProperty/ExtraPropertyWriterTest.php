<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriter;

/**
 * Covers ExtraPropertyWriter::writeAll() with the grouped [module => [property => value]]
 * input: scope routing, storage column resolution, lang array/scalar handling, nullable
 * NULL handling and shop-constraint guards all happen inside the writer.
 */
class ExtraPropertyWriterTest extends TestCase
{
    /** @var array<int, array{sql: string, params: array}> */
    private array $statements = [];

    public function testGroupedValuesAreRoutedPerScope(): void
    {
        $writer = $this->buildWriter();

        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => [
                'reference_code' => 'REF-1',
                'is_dangerous' => true,
                'video_link' => [1 => 'https://en', 2 => 'https://fr'],
                'custom_date' => '2026-06-12 10:00:00',
            ],
        ], ShopConstraint::shop(3));

        $this->assertCount(4, $this->statements);

        // Common scope: one UPSERT with both columns.
        $common = $this->statements[0];
        $this->assertStringContainsString('ps_product_extra`', $common['sql']);
        $this->assertStringContainsString('demoextrafield_reference_code', $common['sql']);
        $this->assertStringContainsString('demoextrafield_is_dangerous', $common['sql']);
        $this->assertSame([7, 'REF-1', true], $common['params']);

        // Lang scope: one UPSERT per language (entityId, shopId, idLang, value).
        $this->assertStringContainsString('ps_product_extra_lang', $this->statements[1]['sql']);
        $this->assertSame([7, 3, 1, 'https://en'], $this->statements[1]['params']);
        $this->assertSame([7, 3, 2, 'https://fr'], $this->statements[2]['params']);

        // Shop scope: (entityId, shopId, value).
        $this->assertStringContainsString('ps_product_extra_shop', $this->statements[3]['sql']);
        $this->assertSame([7, 3, '2026-06-12 10:00:00'], $this->statements[3]['params']);
    }

    public function testLangScalarUsesDefaultLangIdAndIsSkippedWithoutIt(): void
    {
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['video_link' => 'https://scalar'],
        ], ShopConstraint::shop(1), 2);

        $this->assertCount(1, $this->statements);
        $this->assertSame([7, 1, 2, 'https://scalar'], $this->statements[0]['params']);

        $this->statements = [];
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['video_link' => 'https://scalar'],
        ], ShopConstraint::shop(1));

        $this->assertCount(0, $this->statements);
    }

    public function testNullHandlingFollowsNullableFlag(): void
    {
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => [
                'reference_code' => null,           // nullable → persisted as NULL
                'is_dangerous' => null,             // NOT NULL → skipped
                'video_link' => [1 => null],        // nullable lang entry → persisted as NULL
            ],
        ], ShopConstraint::shop(1));

        $this->assertCount(2, $this->statements);
        $this->assertStringNotContainsString('is_dangerous', $this->statements[0]['sql']);
        $this->assertSame([7, null], $this->statements[0]['params']);
        $this->assertSame([7, 1, 1, null], $this->statements[1]['params']);
    }

    public function testAllShopsConstraintSkipsLangAndShopWrites(): void
    {
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => [
                'reference_code' => 'REF-1',
                'video_link' => [1 => 'https://en'],
                'custom_date' => '2026-06-12 10:00:00',
            ],
        ], ShopConstraint::allShops());

        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('ps_product_extra`', $this->statements[0]['sql']);
    }

    public function testUnknownModulesAndPropertiesAreIgnored(): void
    {
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'unknownmodule' => ['reference_code' => 'x'],
            'demoextrafield' => ['unknown_property' => 'y'],
        ], ShopConstraint::shop(1));

        $this->assertCount(0, $this->statements);
    }

    public function testToggleCommonScopeDeducesPrimaryKeyFromDefinition(): void
    {
        $writer = $this->buildWriter();

        $writer->toggleExtraProperty(
            $this->definition('is_dangerous', ExtraPropertyType::BOOL, ExtraPropertyScope::COMMON, nullable: false),
            7,
            ShopConstraint::allShops()
        );

        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('`ps_product_extra`', $this->statements[0]['sql']);
        $this->assertStringContainsString('`id_product`', $this->statements[0]['sql']);
        $this->assertSame([7], $this->statements[0]['params']);
    }

    public function testToggleShopScopeUsesConstraintShopId(): void
    {
        $writer = $this->buildWriter();

        $writer->toggleExtraProperty(
            $this->definition('shop_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::SHOP, nullable: false),
            7,
            ShopConstraint::shop(3)
        );

        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('`ps_product_extra_shop`', $this->statements[0]['sql']);
        $this->assertSame([7, 3], $this->statements[0]['params']);
    }

    public function testToggleShopScopeWithoutSingleShopConstraintThrows(): void
    {
        $writer = $this->buildWriter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('requires a single-shop constraint');

        $writer->toggleExtraProperty(
            $this->definition('shop_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::SHOP, nullable: false),
            7,
            ShopConstraint::allShops()
        );
    }

    public function testToggleNonBoolDefinitionThrows(): void
    {
        $writer = $this->buildWriter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is not of type BOOL');

        $writer->toggleExtraProperty(
            $this->definition('reference_code', ExtraPropertyType::STRING, ExtraPropertyScope::COMMON, nullable: true),
            7,
            ShopConstraint::shop(1)
        );
    }

    private function buildWriter(): ExtraPropertyWriter
    {
        $this->statements = [];

        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(
            static fn (string $identifier): string => '`' . $identifier . '`'
        );
        $connection->method('executeStatement')->willReturnCallback(
            function (string $sql, array $params = []): int {
                $this->statements[] = ['sql' => $sql, 'params' => $params];

                return 1;
            }
        );

        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection([
            $this->definition('reference_code', ExtraPropertyType::STRING, ExtraPropertyScope::COMMON, nullable: true),
            $this->definition('is_dangerous', ExtraPropertyType::BOOL, ExtraPropertyScope::COMMON, nullable: false),
            $this->definition('video_link', ExtraPropertyType::STRING, ExtraPropertyScope::LANG, nullable: true),
            $this->definition('custom_date', ExtraPropertyType::DATE, ExtraPropertyScope::SHOP, nullable: true),
        ]));

        return new ExtraPropertyWriter($connection, 'ps_', $repository);
    }

    private function definition(string $propertyName, ExtraPropertyType $type, ExtraPropertyScope $scope, bool $nullable): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            type: $type,
            scope: $scope,
            moduleName: 'demoextrafield',
            nullable: $nullable,
        );
    }
}
