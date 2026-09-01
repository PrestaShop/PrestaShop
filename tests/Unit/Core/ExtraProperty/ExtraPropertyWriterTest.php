<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriter;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;

/**
 * Covers ExtraPropertyWriter::writeAll() with the grouped [module => [property => value]]
 * input: scope routing, storage column resolution, lang array/scalar handling, nullable
 * NULL handling, shop fan-out (one multi-row UPSERT per table covering every shop of the
 * constraint's scope) and the SHOP-scope association rule (broad scopes only refresh the
 * shops the entity is associated with). The shop list resolver is stubbed with a 2-shop
 * installation (shop 1 and shop 2, both in group 1); the {entity}_shop association query
 * is stubbed through Connection::fetchAllAssociative (both shops associated by default).
 */
class ExtraPropertyWriterTest extends TestCase
{
    private const ALL_SHOP_IDS = [1, 2];

    /** @var array<int, array{sql: string, params: array}> */
    private array $statements = [];

    private string|false $currentToggleValue = false;

    /**
     * Rows returned by the {entity}_shop association query (see filterShopScopeByAssociations).
     *
     * @var array<int, array<string, int>>
     */
    private array $associationRows = [['id_shop' => 1], ['id_shop' => 2]];

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

        $this->assertCount(3, $this->statements);

        // Common scope: one UPSERT with both columns.
        $common = $this->statements[0];
        $this->assertStringContainsString('ps_product_extra`', $common['sql']);
        $this->assertStringContainsString('demoextrafield_reference_code', $common['sql']);
        $this->assertStringContainsString('demoextrafield_is_dangerous', $common['sql']);
        $this->assertSame([7, 'REF-1', true], $common['params']);

        // Lang scope: ONE multi-row UPSERT, one row per language (entityId, shopId, idLang, value).
        $this->assertStringContainsString('ps_product_extra_lang', $this->statements[1]['sql']);
        $this->assertSame([7, 3, 1, 'https://en', 7, 3, 2, 'https://fr'], $this->statements[1]['params']);

        // Shop scope: (entityId, shopId, value).
        $this->assertStringContainsString('ps_product_extra_shop', $this->statements[2]['sql']);
        $this->assertSame([7, 3, '2026-06-12 10:00:00'], $this->statements[2]['params']);
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

    public function testAllShopsConstraintFansOutLangAndShopWritesInBatchedStatements(): void
    {
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => [
                'reference_code' => 'REF-1',
                'video_link' => [1 => 'https://en'],
                'custom_date' => '2026-06-12 10:00:00',
            ],
        ], ShopConstraint::allShops());

        // 1 common + ONE lang statement covering every shop + ONE shop statement covering every shop.
        $this->assertCount(3, $this->statements);
        $this->assertStringContainsString('ps_product_extra`', $this->statements[0]['sql']);
        $this->assertSame([7, 1, 1, 'https://en', 7, 2, 1, 'https://en'], $this->statements[1]['params']);
        $this->assertSame([7, 1, '2026-06-12 10:00:00', 7, 2, '2026-06-12 10:00:00'], $this->statements[2]['params']);
    }

    public function testBroadShopScopeWritesOnlyTheAssociatedShops(): void
    {
        // The entity is only associated with shop 2: a broad (all-shops) save must not
        // create a shop 1 row — native {entity}_shop parity — while the LANG row still
        // covers the full scope, like native lang-multishop writes.
        $this->associationRows = [['id_shop' => 2]];
        $writer = $this->buildWriter();

        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => [
                'video_link' => [1 => 'https://en'],
                'custom_date' => '2026-06-12 10:00:00',
            ],
        ], ShopConstraint::allShops());

        $this->assertCount(2, $this->statements);
        $this->assertSame([7, 1, 1, 'https://en', 7, 2, 1, 'https://en'], $this->statements[0]['params']);
        $this->assertSame([7, 2, '2026-06-12 10:00:00'], $this->statements[1]['params']);
    }

    public function testBroadShopScopeWithoutAnyAssociationWritesNothing(): void
    {
        $this->associationRows = [];
        $writer = $this->buildWriter();

        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['custom_date' => '2026-06-12 10:00:00'],
        ], ShopConstraint::allShops());

        $this->assertCount(0, $this->statements);
    }

    public function testShopCollectionConstraintSkipsTheAssociationFilter(): void
    {
        // An explicitly named shop always gets its row (native CONTEXT_SHOP / id_shop_list
        // parity), even when the entity has no association row for it.
        $this->associationRows = [];
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['custom_date' => '2026-06-12 10:00:00'],
        ], ShopCollection::shops([2]));

        $this->assertCount(1, $this->statements);
        $this->assertSame([7, 2, '2026-06-12 10:00:00'], $this->statements[0]['params']);
    }

    public function testLangWriteOnNonMultishopLangEntityOmitsShopColumn(): void
    {
        // contact_lang has no id_shop: the extra lang table mirrors it, so no shop column,
        // no shop fan-out — one row per language shared by every shop, in one statement.
        $writer = $this->buildWriter();
        $writer->writeAll('contact', 'id_contact', 5, [
            'demoextrafield' => ['job_title' => [1 => 'CEO', 2 => 'PDG']],
        ], ShopConstraint::allShops());

        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('ps_contact_extra_lang', $this->statements[0]['sql']);
        $this->assertStringNotContainsString('id_shop', $this->statements[0]['sql']);
        $this->assertSame([5, 1, 'CEO', 5, 2, 'PDG'], $this->statements[0]['params']);
    }

    public function testToggleCommonScopeDeducesPrimaryKeyFromDefinition(): void
    {
        $writer = $this->buildWriter();

        $writer->toggleExtraProperty(
            $this->definition('is_dangerous', ExtraPropertyType::BOOL, ExtraPropertyScope::COMMON, nullable: false),
            7,
            ShopConstraint::allShops()
        );

        // COMMON rows are shared by every shop: a single upsert, no shop fan-out.
        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('`ps_product_extra`', $this->statements[0]['sql']);
        $this->assertStringContainsString('`id_product`', $this->statements[0]['sql']);
        $this->assertSame([7, 1], $this->statements[0]['params']);
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
        $this->assertSame([7, 3, 1], $this->statements[0]['params']);
    }

    public function testToggleShopScopeFansOutAndUniformizesAcrossConstraintShops(): void
    {
        // The representative shop's current value (true) decides the target (false),
        // written to every associated shop of the scope in ONE multi-row statement —
        // divergent shops end up aligned.
        $this->currentToggleValue = '1';
        $writer = $this->buildWriter();

        $writer->toggleExtraProperty(
            $this->definition('shop_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::SHOP, nullable: false),
            7,
            ShopConstraint::allShops()
        );

        $this->assertCount(1, $this->statements);
        $this->assertSame([7, 1, 0, 7, 2, 0], $this->statements[0]['params']);
    }

    public function testToggleShopScopeOnlyTouchesAssociatedShops(): void
    {
        $this->associationRows = [['id_shop' => 2]];
        $writer = $this->buildWriter();

        $writer->toggleExtraProperty(
            $this->definition('shop_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::SHOP, nullable: false),
            7,
            ShopConstraint::allShops()
        );

        $this->assertCount(1, $this->statements);
        $this->assertSame([7, 2, 1], $this->statements[0]['params']);
    }

    public function testToggleLangScopeWritesLangAndShopColumns(): void
    {
        $writer = $this->buildWriter();

        $writer->toggleExtraProperty(
            $this->definition('lang_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::LANG, nullable: false),
            7,
            ShopConstraint::shop(3),
            2
        );

        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('`ps_product_extra_lang`', $this->statements[0]['sql']);
        $this->assertSame([7, 3, 2, 1], $this->statements[0]['params']);
    }

    public function testToggleLangScopeWithoutLangIdThrows(): void
    {
        $writer = $this->buildWriter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('requires a language id');

        $writer->toggleExtraProperty(
            $this->definition('lang_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::LANG, nullable: false),
            7,
            ShopConstraint::shop(1)
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
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $connection->method('createQueryBuilder')->willReturnCallback(
            fn (): QueryBuilder => new QueryBuilder($connection)
        );
        $connection->method('executeStatement')->willReturnCallback(
            function (string $sql, array $params = []): int {
                $this->statements[] = ['sql' => $sql, 'params' => $params];

                return 1;
            }
        );
        $connection->method('fetchOne')->willReturnCallback(
            fn (): string|false => $this->currentToggleValue
        );
        // The only fetchAllAssociative issued by the writer is the {entity}_shop association lookup.
        $connection->method('fetchAllAssociative')->willReturnCallback(
            fn (): array => $this->associationRows
        );

        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection([
            $this->definition('reference_code', ExtraPropertyType::STRING, ExtraPropertyScope::COMMON, nullable: true),
            $this->definition('is_dangerous', ExtraPropertyType::BOOL, ExtraPropertyScope::COMMON, nullable: false),
            $this->definition('video_link', ExtraPropertyType::STRING, ExtraPropertyScope::LANG, nullable: true),
            $this->definition('custom_date', ExtraPropertyType::DATE, ExtraPropertyScope::SHOP, nullable: true),
            $this->definition('job_title', ExtraPropertyType::STRING, ExtraPropertyScope::LANG, nullable: true, entityName: 'contact', multiShop: false),
        ]));

        return new ExtraPropertyWriter($connection, 'ps_', $repository, $this->buildShopListResolver());
    }

    /**
     * Stubs a 2-shop installation: shop() → that shop, ShopCollection → its ids,
     * group/all → shops 1 and 2; the representative shop is the lowest of the scope.
     */
    private function buildShopListResolver(): ShopListResolverInterface
    {
        $resolver = $this->createMock(ShopListResolverInterface::class);
        $resolver->method('resolveShopIds')->willReturnCallback(
            static function (ShopConstraint $shopConstraint): array {
                if (null !== $shopConstraint->getShopId()) {
                    return [$shopConstraint->getShopId()->getValue()];
                }
                if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
                    return array_map(static fn (ShopId $shopId): int => $shopId->getValue(), $shopConstraint->getShopIds());
                }

                return self::ALL_SHOP_IDS;
            }
        );
        $resolver->method('resolveRepresentativeShopId')->willReturnCallback(
            fn (ShopConstraint $shopConstraint): int => min($resolver->resolveShopIds($shopConstraint))
        );

        return $resolver;
    }

    private function definition(
        string $propertyName,
        ExtraPropertyType $type,
        ExtraPropertyScope $scope,
        bool $nullable,
        string $entityName = 'product',
        ?bool $multiShop = null,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: $entityName,
            propertyName: $propertyName,
            type: $type,
            scope: $scope,
            moduleName: 'demoextrafield',
            nullable: $nullable,
            multiShop: $multiShop ?? (ExtraPropertyScope::COMMON !== $scope),
        );
    }
}
