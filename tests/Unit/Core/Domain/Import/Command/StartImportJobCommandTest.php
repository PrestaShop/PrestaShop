<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Import\Command;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\StartImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Every constraint the command enforces, and the code it reports.
 *
 * The previous command deferred half its validation into getters, so an invalid one could be
 * built and only explode once a handler called the right accessor.
 */
class StartImportJobCommandTest extends TestCase
{
    public function testItBuildsWithTheWizardConfiguration(): void
    {
        $command = new StartImportJobCommand(
            '/tmp/products.csv',
            'product',
            'en',
            ShopConstraint::shop(2),
            [0 => 'name', 1 => 'price', 2 => 'no'],
            ['truncate' => true, 'batchLimit' => 250, 'mymodule_flag' => 'kept'],
            ',',
            '|',
            3
        );

        $this->assertSame('/tmp/products.csv', $command->getSourceFilePath());
        $this->assertSame('product', $command->getEntityType()->getValue());
        $this->assertSame('en', $command->getLangIso());
        $this->assertSame(2, $command->getShopConstraint()->getShopId()->getValue());
        $this->assertSame([0 => 'name', 1 => 'price', 2 => 'no'], $command->getFieldMapping()->getValue());
        $this->assertSame(',', $command->getCsvSeparator());
        $this->assertSame('|', $command->getMultipleValueSeparator());
        $this->assertSame(3, $command->getSkipRows());
        $this->assertSame(
            ['truncate' => true, 'batchLimit' => 250, 'mymodule_flag' => 'kept'],
            $command->getOptions(),
            'Options travel as an open blob; an importer-specific key must not be filtered out here'
        );
    }

    public function testTheDefaultsAreTheWizardDefaults(): void
    {
        $command = $this->buildCommand();

        $this->assertSame(';', $command->getCsvSeparator());
        $this->assertSame(',', $command->getMultipleValueSeparator());
        $this->assertSame(1, $command->getSkipRows(), 'A CSV export has a header row by default');
        $this->assertSame([], $command->getOptions());
    }

    public function testAMultiCharacterMultipleValueSeparatorIsAllowed(): void
    {
        // unlike the csv separator, it is not handed to fgetcsv and no column bounds it
        $this->assertSame('||', $this->buildCommand(['multipleValueSeparator' => '||'])->getMultipleValueSeparator());
    }

    /**
     * @dataProvider provideInvalidConfigurations
     *
     * @param array<string, mixed> $overrides
     */
    public function testItRefusesAnInvalidConfiguration(array $overrides, int $expectedCode): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode($expectedCode);

        $this->buildCommand($overrides);
    }

    public static function provideInvalidConfigurations(): Generator
    {
        yield 'empty source path' => [
            ['sourceFilePath' => ''],
            ImportJobConstraintException::INVALID_SOURCE_PATH,
        ];
        yield 'empty entity type' => [
            ['entityType' => ''],
            ImportJobConstraintException::INVALID_ENTITY_TYPE,
        ];
        yield 'entity type is not snake case' => [
            ['entityType' => 'Product'],
            ImportJobConstraintException::INVALID_ENTITY_TYPE,
        ];
        yield 'entity type longer than its column' => [
            ['entityType' => str_repeat('a', 65)],
            ImportJobConstraintException::INVALID_ENTITY_TYPE,
        ];
        yield 'language is not a two-letter code' => [
            ['langIso' => 'eng'],
            ImportJobConstraintException::INVALID_LANG_ISO,
        ];
        yield 'empty language' => [
            ['langIso' => ''],
            ImportJobConstraintException::INVALID_LANG_ISO,
        ];
        yield 'multi-character csv separator' => [
            ['csvSeparator' => '||'],
            ImportJobConstraintException::INVALID_CSV_SEPARATOR,
        ];
        yield 'empty csv separator' => [
            ['csvSeparator' => ''],
            ImportJobConstraintException::INVALID_CSV_SEPARATOR,
        ];
        yield 'empty multiple value separator' => [
            ['multipleValueSeparator' => ''],
            ImportJobConstraintException::INVALID_MULTIPLE_VALUE_SEPARATOR,
        ];
        yield 'negative skipped rows' => [
            ['skipRows' => -1],
            ImportJobConstraintException::INVALID_SKIP_ROWS,
        ];
        yield 'batch limit of zero' => [
            ['options' => ['batchLimit' => 0]],
            ImportJobConstraintException::INVALID_BATCH_LIMIT,
        ];
        yield 'non-numeric batch limit' => [
            ['options' => ['batchLimit' => 'lots']],
            ImportJobConstraintException::INVALID_BATCH_LIMIT,
        ];
        yield 'empty column mapping' => [
            ['fieldMapping' => []],
            ImportJobConstraintException::INVALID_COLUMN_MAPPING,
        ];
        yield 'column mapping feeding no field at all' => [
            ['fieldMapping' => [0 => 'no', 1 => 'no']],
            ImportJobConstraintException::INVALID_COLUMN_MAPPING,
        ];
        yield 'column mapping keyed by something other than a column index' => [
            ['fieldMapping' => ['name' => 'name']],
            ImportJobConstraintException::INVALID_COLUMN_MAPPING,
        ];
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function buildCommand(array $overrides = []): StartImportJobCommand
    {
        $values = $overrides + [
            'sourceFilePath' => '/tmp/products.csv',
            'entityType' => 'product',
            'langIso' => 'en',
            'shopConstraint' => ShopConstraint::shop(1),
            'fieldMapping' => [0 => 'name'],
        ];

        return new StartImportJobCommand(
            $values['sourceFilePath'],
            $values['entityType'],
            $values['langIso'],
            $values['shopConstraint'],
            $values['fieldMapping'],
            $overrides['options'] ?? [],
            $overrides['csvSeparator'] ?? ';',
            $overrides['multipleValueSeparator'] ?? ',',
            $overrides['skipRows'] ?? 1
        );
    }
}
