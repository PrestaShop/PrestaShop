<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\Exception as DriverException;
use Doctrine\DBAL\Exception\DeadlockException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriter;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use RuntimeException;

/**
 * Covers ExtraPropertyWriter::writeAll() with the grouped [module => [property => value]]
 * input: scope routing, storage column resolution, lang array/scalar handling, nullable
 * NULL handling, shop fan-out (one UPDATE per row) and the SHOP-scope association rule (broad scopes only refresh the
 * shops the entity is associated with). The shop list resolver is stubbed with a 2-shop
 * installation (shop 1 and shop 2, both in group 1); the {entity}_shop association query
 * is stubbed through Connection::fetchAllAssociative (both shops associated by default).
 */
class ExtraPropertyWriterTest extends TestCase
{
    private const ALL_SHOP_IDS = [1, 2];

    /** @var array<int, array{sql: string, params: array}> */
    private array $statements = [];

    private bool|string|null $currentToggleValue = false;

    /**
     * Rows returned by the {entity}_shop association query (see filterShopScopeByAssociations).
     *
     * @var array<int, array<string, int>>
     */
    private array $associationRows = [['id_shop' => 1], ['id_shop' => 2]];

    /** @var list<string> */
    private array $associationQueries = [];

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

        // Common scope: one UPDATE with both columns, data bound before the key.
        $common = $this->statements[0];
        $this->assertStringContainsString('ps_product_extra`', $common['sql']);
        $this->assertStringContainsString('demoextrafield_reference_code', $common['sql']);
        $this->assertStringContainsString('demoextrafield_is_dangerous', $common['sql']);
        $this->assertSame(['REF-1', 1, 7], $common['params']);

        // Lang scope: one UPDATE per language, with the full storage identity.
        $this->assertStringContainsString('ps_product_extra_lang', $this->statements[1]['sql']);
        $this->assertSame(['https://en', 7, 3, 1], $this->statements[1]['params']);
        $this->assertSame(['https://fr', 7, 3, 2], $this->statements[2]['params']);

        // Shop scope: (value, entityId, shopId).
        $this->assertStringContainsString('ps_product_extra_shop', $this->statements[3]['sql']);
        $this->assertSame(['2026-06-12 10:00:00', 7, 3], $this->statements[3]['params']);
    }

    public function testLangScalarUsesDefaultLangIdAndIsSkippedWithoutIt(): void
    {
        $writer = $this->buildWriter();
        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['video_link' => 'https://scalar'],
        ], ShopConstraint::shop(1), 2);

        $this->assertCount(1, $this->statements);
        $this->assertSame(['https://scalar', 7, 1, 2], $this->statements[0]['params']);

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
        $this->assertSame([null, 7], $this->statements[0]['params']);
        $this->assertSame([null, 7, 1, 1], $this->statements[1]['params']);
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

        // One common UPDATE, two lang UPDATEs and two shop UPDATEs.
        $this->assertCount(5, $this->statements);
        $this->assertStringContainsString('ps_product_extra`', $this->statements[0]['sql']);
        $this->assertSame(['https://en', 7, 1, 1], $this->statements[1]['params']);
        $this->assertSame(['https://en', 7, 2, 1], $this->statements[2]['params']);
        $this->assertSame(['2026-06-12 10:00:00', 7, 1], $this->statements[3]['params']);
        $this->assertSame(['2026-06-12 10:00:00', 7, 2], $this->statements[4]['params']);
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

        $this->assertCount(3, $this->statements);
        $this->assertSame(['https://en', 7, 1, 1], $this->statements[0]['params']);
        $this->assertSame(['https://en', 7, 2, 1], $this->statements[1]['params']);
        $this->assertSame(['2026-06-12 10:00:00', 7, 2], $this->statements[2]['params']);
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
        $this->assertSame(['2026-06-12 10:00:00', 7, 2], $this->statements[0]['params']);
    }

    public function testLangWriteOnNonMultishopLangEntityOmitsShopColumn(): void
    {
        // contact_lang has no id_shop: the extra lang table mirrors it, so no shop column,
        // no shop fan-out — one row per language shared by every shop.
        $writer = $this->buildWriter();
        $writer->writeAll('contact', 'id_contact', 5, [
            'demoextrafield' => ['job_title' => [1 => 'CEO', 2 => 'PDG']],
        ], ShopConstraint::allShops());

        $this->assertCount(2, $this->statements);
        $this->assertStringContainsString('ps_contact_extra_lang', $this->statements[0]['sql']);
        $this->assertStringNotContainsString('id_shop', $this->statements[0]['sql']);
        $this->assertSame(['CEO', 5, 1], $this->statements[0]['params']);
        $this->assertSame(['PDG', 5, 2], $this->statements[1]['params']);
    }

    public function testDefinitionRestrictionIntersectsTheFanOutPerDefinition(): void
    {
        // custom_date (SHOP scope) is restricted to shop 2, video_link (LANG scope) is not:
        // a broad save fans the lang rows out to both shops but the shop row only to shop 2.
        $writer = $this->buildWriter(['custom_date' => [2]]);

        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => [
                'video_link' => [1 => 'https://en'],
                'custom_date' => '2026-06-12 10:00:00',
            ],
        ], ShopConstraint::allShops());

        $this->assertCount(3, $this->statements);
        $this->assertSame(['https://en', 7, 1, 1], $this->statements[0]['params']);
        $this->assertSame(['https://en', 7, 2, 1], $this->statements[1]['params']);
        $this->assertSame(['2026-06-12 10:00:00', 7, 2], $this->statements[2]['params']);
    }

    public function testDefinitionsWithDifferentRestrictionsGetSeparateStatements(): void
    {
        // Two SHOP-scope definitions with different effective shop sets cannot share one
        // batch: each bucket covers its own shops.
        $writer = $this->buildWriter(['custom_date' => [1]]);

        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => [
                'custom_date' => '2026-06-12 10:00:00',
                'shop_note' => 'hello',
            ],
        ], ShopConstraint::allShops());

        $this->assertCount(3, $this->statements);
        $this->assertSame(['2026-06-12 10:00:00', 7, 1], $this->statements[0]['params']);
        $this->assertSame(['hello', 7, 1], $this->statements[1]['params']);
        $this->assertSame(['hello', 7, 2], $this->statements[2]['params']);
    }

    public function testDefinitionNotAvailableInTheScopeIsSkippedEntirely(): void
    {
        // reference_code (COMMON scope, single shared row) is restricted to a shop outside
        // the write scope: its value is dropped, like the reader that would never surface it.
        $writer = $this->buildWriter(['reference_code' => [2]]);

        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['reference_code' => 'REF-1'],
        ], ShopConstraint::shop(1));

        $this->assertCount(0, $this->statements);
    }

    public function testLangRestrictionLimitsTheLangFanOut(): void
    {
        // Unlike the {entity}_shop association rule (which LANG writes ignore for native
        // parity), the definition-level restriction applies to LANG rows too.
        $writer = $this->buildWriter(['video_link' => [2]]);

        $writer->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['video_link' => [1 => 'https://en']],
        ], ShopConstraint::allShops());

        $this->assertCount(1, $this->statements);
        $this->assertSame(['https://en', 7, 2, 1], $this->statements[0]['params']);
    }

    public function testToggleIsANoOpWhenTheDefinitionIsNotAvailableOnTheShop(): void
    {
        // Definition availability applies even to an explicitly named shop — unlike the
        // entity association rule, the definition simply does not exist there.
        $writer = $this->buildWriter(['shop_flag' => [2]]);

        $writer->toggleExtraProperty(
            $this->definition('shop_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::SHOP, nullable: false),
            7,
            ShopConstraint::shop(1)
        );

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

        // COMMON rows are shared by every shop: a single UPDATE, no shop fan-out.
        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('`ps_product_extra`', $this->statements[0]['sql']);
        $this->assertStringContainsString('`id_product`', $this->statements[0]['sql']);
        $this->assertSame([1, 7], $this->statements[0]['params']);
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
        $this->assertSame([1, 7, 3], $this->statements[0]['params']);
    }

    public function testToggleShopScopeFansOutAndUniformizesAcrossConstraintShops(): void
    {
        // The representative shop's current value (true) decides the target (false),
        // written to every associated shop of the scope in an atomic batch —
        // divergent shops end up aligned.
        $this->currentToggleValue = '1';
        $writer = $this->buildWriter();

        $writer->toggleExtraProperty(
            $this->definition('shop_flag', ExtraPropertyType::BOOL, ExtraPropertyScope::SHOP, nullable: false),
            7,
            ShopConstraint::allShops()
        );

        $this->assertCount(2, $this->statements);
        $this->assertSame([0, 7, 1], $this->statements[0]['params']);
        $this->assertSame([0, 7, 2], $this->statements[1]['params']);
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
        $this->assertSame([1, 7, 2], $this->statements[0]['params']);
    }

    public function testToggleShopScopeUsesPhysicalTableNameForAssociationLookup(): void
    {
        $this->associationRows = [['id_shop' => 1]];
        $writer = $this->buildWriter();
        $definition = new ExtraPropertyDefinition(
            entityName: 'cms_page',
            propertyName: 'shop_flag',
            type: ExtraPropertyType::BOOL,
            scope: ExtraPropertyScope::SHOP,
            moduleName: 'demoextrafield',
            nullable: false,
            multiShop: true,
            primaryKeyName: 'id_cms',
        );

        $writer->toggleExtraProperty(
            $definition,
            7,
            ShopConstraint::allShops()
        );

        $this->assertCount(1, $this->associationQueries);
        $this->assertStringContainsString('FROM ps_cms_shop ', $this->associationQueries[0]);
        $this->assertStringNotContainsString('FROM ps_cms_page_shop ', $this->associationQueries[0]);
        $this->assertStringContainsString('a.`id_cms` = :entityId', $this->associationQueries[0]);
        $this->assertCount(1, $this->statements);
        $this->assertStringContainsString('`ps_cms_extra_shop`', $this->statements[0]['sql']);
        $this->assertSame([1, 7, 1], $this->statements[0]['params']);
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
        $this->assertSame([1, 7, 3, 2], $this->statements[0]['params']);
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

    /**
     * @param array<string, list<int>> $shopRestrictionsByProperty Definition-level shop
     *                                                             restrictions (propertyName → shop ids); absent = unrestricted
     */
    private function buildWriter(array $shopRestrictionsByProperty = [], ?Connection $storageConnection = null): ExtraPropertyWriter
    {
        $this->statements = [];
        $this->associationQueries = [];

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
            fn (): bool|string|null => $this->currentToggleValue
        );
        // The only fetchAllAssociative issued by the writer is the {entity}_shop association lookup.
        $connection->method('fetchAllAssociative')->willReturnCallback(
            function (string $sql): array {
                $this->associationQueries[] = $sql;

                return $this->associationRows;
            }
        );

        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection([
            $this->definition('reference_code', ExtraPropertyType::STRING, ExtraPropertyScope::COMMON, nullable: true),
            $this->definition('is_dangerous', ExtraPropertyType::BOOL, ExtraPropertyScope::COMMON, nullable: false),
            $this->definition('video_link', ExtraPropertyType::STRING, ExtraPropertyScope::LANG, nullable: true),
            $this->definition('custom_date', ExtraPropertyType::DATE, ExtraPropertyScope::SHOP, nullable: true),
            $this->definition('shop_note', ExtraPropertyType::STRING, ExtraPropertyScope::SHOP, nullable: true),
            $this->definition('job_title', ExtraPropertyType::STRING, ExtraPropertyScope::LANG, nullable: true, entityName: 'contact', multiShop: false),
        ]));

        return new ExtraPropertyWriter($storageConnection ?? $connection, 'ps_', $repository, $this->buildShopListResolver(), $this->buildShopFilter($shopRestrictionsByProperty));
    }

    public function testUnchangedRowDoesNotCauseAnInsert(): void
    {
        $connection = $this->storageConnection();
        $connection->expects($this->exactly(2))->method('executeStatement')
            ->with('UPDATE `ps_product_extra` SET `demoextrafield_reference_code` = ? WHERE `id_product` = ?', ['SAME', 7])
            ->willReturn(0);
        $connection->expects($this->once())->method('fetchOne')
            ->with('SELECT 1 FROM `ps_product_extra` WHERE `id_product` = ? FOR UPDATE', [7])
            ->willReturn(1);
        $connection->expects($this->once())->method('beginTransaction');
        $connection->expects($this->once())->method('commit');

        $this->writeReference($connection);
    }

    public function testRowInsertedBetweenUpdateAndExistenceCheckIsUpdated(): void
    {
        $connection = $this->storageConnection();
        $connection->expects($this->exactly(2))->method('executeStatement')
            ->with('UPDATE `ps_product_extra` SET `demoextrafield_reference_code` = ? WHERE `id_product` = ?', ['SAME', 7])
            ->willReturnOnConsecutiveCalls(0, 1);
        $connection->expects($this->once())->method('fetchOne')
            ->with('SELECT 1 FROM `ps_product_extra` WHERE `id_product` = ? FOR UPDATE', [7])
            ->willReturn(1);
        $connection->expects($this->once())->method('beginTransaction');
        $connection->expects($this->once())->method('commit');

        $this->writeReference($connection);
    }

    public function testMissingRowIsInserted(): void
    {
        $connection = $this->storageConnection();
        $calls = [];
        $connection->expects($this->exactly(2))->method('executeStatement')->willReturnCallback(
            function (string $sql, array $params) use (&$calls): int {
                $calls[] = [$sql, $params];

                return count($calls) - 1;
            }
        );
        $connection->expects($this->once())->method('fetchOne')->willReturn(false);

        $this->writeReference($connection);

        $this->assertSame(['SAME', 7], $calls[0][1]);
        $this->assertSame('INSERT INTO `ps_product_extra` (`id_product`, `demoextrafield_reference_code`) VALUES (?, ?)', $calls[1][0]);
        $this->assertSame([7, 'SAME'], $calls[1][1]);
    }

    /**
     * @dataProvider concurrentInsertProvider
     */
    public function testConcurrentInsertRetriesOnlyTheSameIdentity(int $lockedAffectedRows): void
    {
        $connection = $this->storageConnection();
        $duplicate = $this->duplicateException();
        $call = 0;
        $connection->expects($this->exactly(3))->method('executeStatement')->willReturnCallback(
            function (string $sql, array $params) use (&$call, $duplicate, $lockedAffectedRows): int {
                ++$call;
                if (2 === $call) {
                    $this->assertStringStartsWith('INSERT INTO ', $sql);
                    throw $duplicate;
                }
                $this->assertSame('UPDATE `ps_product_extra` SET `demoextrafield_reference_code` = ? WHERE `id_product` = ?', $sql);
                $this->assertSame(['SAME', 7], $params);

                return 1 === $call ? 0 : $lockedAffectedRows;
            }
        );
        $read = 0;
        $connection->expects($this->exactly(2))->method('fetchOne')
            ->with('SELECT 1 FROM `ps_product_extra` WHERE `id_product` = ? FOR UPDATE', [7])
            ->willReturnCallback(
                static function () use (&$read): int|false {
                    return 0 === $read++ ? false : 1;
                }
            );

        $this->writeReference($connection);
    }

    public static function concurrentInsertProvider(): array
    {
        return [
            'different value' => [1],
            'identical value' => [0],
        ];
    }

    public function testSecondaryUniqueConflictIsNotSilentlyAccepted(): void
    {
        $connection = $this->storageConnection();
        $duplicate = $this->duplicateException();
        $call = 0;
        $connection->expects($this->exactly(2))->method('executeStatement')->willReturnCallback(
            static function () use (&$call, $duplicate): int {
                if (2 === ++$call) {
                    throw $duplicate;
                }

                return 0;
            }
        );
        $connection->expects($this->exactly(2))->method('fetchOne')->willReturn(false);
        $connection->expects($this->once())->method('beginTransaction');
        $connection->expects($this->once())->method('rollBack');
        $connection->expects($this->never())->method('commit');

        $this->expectExceptionObject($duplicate);
        $this->writeReference($connection);
    }

    public function testUniqueConflictOnUpdateIsPropagatedWithoutInserting(): void
    {
        $connection = $this->storageConnection();
        $duplicate = $this->duplicateException();
        $connection->expects($this->once())->method('executeStatement')->willThrowException($duplicate);
        $connection->expects($this->never())->method('fetchOne');
        $connection->expects($this->never())->method('beginTransaction');

        $this->expectExceptionObject($duplicate);
        $this->writeReference($connection);
    }

    public function testChangedRowDoesNotOpenATransaction(): void
    {
        $connection = $this->storageConnection();
        $connection->expects($this->once())->method('executeStatement')->willReturn(1);
        $connection->expects($this->never())->method('fetchOne');
        $connection->expects($this->never())->method('beginTransaction');
        $connection->expects($this->never())->method('commit');

        $this->writeReference($connection);
    }

    /**
     * @return Connection&MockObject
     */
    private function storageConnection(): Connection
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(static fn (string $name): string => '`' . $name . '`');
        $active = false;
        $connection->method('isTransactionActive')->willReturnCallback(static function () use (&$active): bool {
            return $active;
        });
        $connection->method('beginTransaction')->willReturnCallback(static function () use (&$active): bool {
            return $active = true;
        });
        foreach (['commit', 'rollBack'] as $method) {
            $connection->method($method)->willReturnCallback(static function () use (&$active): bool {
                $active = false;

                return true;
            });
        }

        return $connection;
    }

    /**
     * @dataProvider batchTransactionProvider
     */
    public function testBatchOwnsOnlyItsTransactionBoundary(bool $outerTransaction, bool $fail): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(static fn (string $name): string => '`' . $name . '`');
        $connection->method('isTransactionActive')->willReturnOnConsecutiveCalls($outerTransaction, true);
        $connection->expects($outerTransaction ? $this->never() : $this->once())->method('beginTransaction');
        $connection->expects(!$outerTransaction && !$fail ? $this->once() : $this->never())->method('commit');
        $connection->expects(!$outerTransaction && $fail ? $this->once() : $this->never())->method('rollBack');
        $connection->expects($this->never())->method('setRollbackOnly');
        $savepoint = null;
        $connection->expects($outerTransaction ? $this->once() : $this->never())->method('createSavepoint')->willReturnCallback(
            static function (string $name) use (&$savepoint): void {
                $savepoint = $name;
            }
        );
        $connection->expects($outerTransaction && $fail ? $this->once() : $this->never())->method('rollbackSavepoint')->willReturnCallback(
            function (string $name) use (&$savepoint): void {
                $this->assertSame($savepoint, $name);
            }
        );
        $connection->expects($outerTransaction ? $this->once() : $this->never())->method('releaseSavepoint')->willReturnCallback(
            function (string $name) use (&$savepoint): void {
                $this->assertSame($savepoint, $name);
            }
        );
        $duplicate = $this->duplicateException();
        $call = 0;
        $connection->expects($this->exactly(2))->method('executeStatement')->willReturnCallback(
            function (string $sql, array $params) use (&$call, $duplicate, $fail): int {
                $this->assertStringContainsString('WHERE `id_product` = ? AND `id_shop` = ? AND `id_lang` = ?', $sql);
                $this->assertSame(['value', 7, 1, ++$call], $params);
                if ($fail && 2 === $call) {
                    throw $duplicate;
                }

                return 1;
            }
        );
        $connection->expects($this->never())->method('fetchOne');
        if ($fail) {
            $this->expectExceptionObject($duplicate);
        }

        $this->buildWriter([], $connection)->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['video_link' => [1 => 'value', 2 => 'value']],
        ], ShopConstraint::shop(1));
    }

    public static function batchTransactionProvider(): array
    {
        return [
            'owned success' => [false, false],
            'owned failure' => [false, true],
            'outer success' => [true, false],
            'outer failure' => [true, true],
        ];
    }

    /**
     * @dataProvider failedCleanupProvider
     */
    public function testFailedCleanupIsReportedAndTheTransactionCannotBeReused(bool $outerTransaction, string $cleanupMethod): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(static fn (string $name): string => '`' . $name . '`');
        $connection->method('isTransactionActive')->willReturnOnConsecutiveCalls($outerTransaction, true, true);
        $connection->expects($outerTransaction ? $this->never() : $this->once())->method('beginTransaction');
        $connection->expects($outerTransaction ? $this->once() : $this->never())->method('createSavepoint');
        $connection->expects($this->never())->method('commit');
        $writeError = new RuntimeException('write failure');
        $cleanupError = new RuntimeException('cleanup failure');
        $connection->expects($this->once())->method('executeStatement')->willThrowException($writeError);
        $connection->expects($this->once())->method($cleanupMethod)->willThrowException($cleanupError);
        if ('releaseSavepoint' === $cleanupMethod) {
            $connection->expects($this->once())->method('rollbackSavepoint');
        } elseif ('rollbackSavepoint' === $cleanupMethod) {
            $connection->expects($this->never())->method('releaseSavepoint');
        }
        if ($outerTransaction) {
            $connection->expects($this->never())->method('rollBack');
        }
        $connection->expects($outerTransaction ? $this->once() : $this->never())->method('setRollbackOnly');
        $connection->expects($outerTransaction ? $this->never() : $this->once())->method('close');

        try {
            $this->buildWriter([], $connection)->writeAll('product', 'id_product', 7, [
                'demoextrafield' => ['video_link' => [1 => 'value', 2 => 'value']],
            ], ShopConstraint::shop(1));
            $this->fail('A failed cleanup must be reported.');
        } catch (RuntimeException $e) {
            $this->assertSame($writeError, $e->getPrevious());
            $this->assertStringContainsString('write failure', $e->getMessage());
            $this->assertStringContainsString('cleanup failure', $e->getMessage());
        }
    }

    public static function failedCleanupProvider(): array
    {
        return [
            'owned rollback' => [false, 'rollBack'],
            'outer rollback savepoint' => [true, 'rollbackSavepoint'],
            'outer release savepoint after rollback' => [true, 'releaseSavepoint'],
        ];
    }

    /**
     * @dataProvider externalDeadlockProvider
     */
    public function testExternalDeadlockMarksTransactionRollbackOnlyWithoutRetry(bool $batch): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(static fn (string $name): string => '`' . $name . '`');
        $connection->method('isTransactionActive')->willReturn(true);
        $deadlock = new DeadlockException(new DriverException('Deadlock', '40001', 1213), null);
        $connection->expects($this->once())->method('executeStatement')->willThrowException($deadlock);
        $connection->expects($this->never())->method('fetchOne');
        $connection->expects($this->never())->method('beginTransaction');
        $connection->expects($this->never())->method('commit');
        $connection->expects($this->never())->method('rollBack');
        $connection->expects($batch ? $this->once() : $this->never())->method('createSavepoint');
        $connection->expects($this->never())->method('rollbackSavepoint');
        $connection->expects($this->never())->method('releaseSavepoint');
        $connection->expects($this->once())->method('setRollbackOnly');
        $connection->expects($this->never())->method('close');

        $this->expectExceptionObject($deadlock);
        $this->buildWriter([], $connection)->writeAll('product', 'id_product', 7, [
            'demoextrafield' => $batch ? ['video_link' => [1 => 'value', 2 => 'value']] : ['reference_code' => 'value'],
        ], ShopConstraint::shop(1));
    }

    public static function externalDeadlockProvider(): array
    {
        return ['single row' => [false], 'batch' => [true]];
    }

    /**
     * @dataProvider externalDeadlockProvider
     */
    public function testOwnedDeadlockDiscardsTheConnectionAndPreservesTheException(bool $batch): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(static fn (string $name): string => '`' . $name . '`');
        $connection->method('isTransactionActive')->willReturn(false);
        $deadlock = new DeadlockException(new DriverException('Deadlock', '40001', 1213), null);
        if ($batch) {
            $connection->expects($this->once())->method('executeStatement')->willThrowException($deadlock);
            $connection->expects($this->never())->method('fetchOne');
        } else {
            $connection->expects($this->once())->method('executeStatement')->willReturn(0);
            $connection->expects($this->once())->method('fetchOne')->willThrowException($deadlock);
        }
        $connection->expects($this->once())->method('beginTransaction');
        $connection->expects($this->never())->method('commit');
        $connection->expects($this->never())->method('rollBack');
        $connection->expects($this->never())->method('setRollbackOnly');
        $connection->expects($this->once())->method('close');

        $this->expectExceptionObject($deadlock);
        $this->buildWriter([], $connection)->writeAll('product', 'id_product', 7, [
            'demoextrafield' => $batch ? ['video_link' => [1 => 'value', 2 => 'value']] : ['reference_code' => 'value'],
        ], ShopConstraint::shop(1));
    }

    private function duplicateException(): UniqueConstraintViolationException
    {
        $driver = new DriverException('Duplicate entry', '23000', 1062);

        return new UniqueConstraintViolationException($driver, null);
    }

    private function writeReference(Connection $connection): void
    {
        $this->buildWriter([], $connection)->writeAll('product', 'id_product', 7, [
            'demoextrafield' => ['reference_code' => 'SAME'],
        ], ShopConstraint::shop(1));
    }

    /**
     * Definition shop filter honoring the given per-property restrictions (unrestricted
     * pass-through by default, i.e. multistore-off behavior — the pre-existing scenarios
     * keep their exact expectations). Availability semantics mirror
     * ExtraPropertyDefinition::isAvailableForShops().
     *
     * @param array<string, list<int>> $shopRestrictionsByProperty
     */
    private function buildShopFilter(array $shopRestrictionsByProperty = []): ExtraPropertyDefinitionShopFilterInterface
    {
        $availableShopIds = static function (ExtraPropertyDefinition $definition, array $shopIds) use ($shopRestrictionsByProperty): array {
            $restriction = $shopRestrictionsByProperty[$definition->getPropertyName()] ?? null;

            return null === $restriction ? $shopIds : array_values(array_intersect($shopIds, $restriction));
        };

        $filter = $this->createMock(ExtraPropertyDefinitionShopFilterInterface::class);
        $filter->method('getAvailableShopIds')->willReturnCallback($availableShopIds);
        $filter->method('filterByShopIds')->willReturnCallback(
            static fn (ExtraPropertyDefinitionCollection $definitions, array $shopIds): ExtraPropertyDefinitionCollection => new ExtraPropertyDefinitionCollection(array_values(array_filter(
                iterator_to_array($definitions),
                static fn (ExtraPropertyDefinition $definition): bool => !isset($shopRestrictionsByProperty[$definition->getPropertyName()])
                    || [] !== $availableShopIds($definition, $shopIds)
            )))
        );
        $filter->method('filterByShopConstraint')->willReturnArgument(0);

        return $filter;
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
