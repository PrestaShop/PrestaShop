<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use AdminImportController;
use Context;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;

/**
 * The import screen pre-selects a field for each column by POSITION - getTypeValuesOptions() marks the
 * option whose index equals the column index plus one, walking available_fields in declaration order.
 * The shipped sample file is therefore only usable without manual mapping while its columns line up with
 * that list, and it has silently drifted out of line twice already.
 */
class ProductsImportSampleTest extends KernelTestCase
{
    private const SAMPLE = _PS_ROOT_DIR_ . '/docs/csv_import/products_import.csv';
    private const ENTITY_PRODUCTS = 1;

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $availableFields;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        Context::getContext()->container = self::getContainer();

        $_GET['entity'] = self::ENTITY_PRODUCTS;
        Tools::resetRequest();
        $controller = new AdminImportController();
        $this->availableFields = $controller->available_fields;
        unset($_GET['entity']);
        Tools::resetRequest();
    }

    public function testEveryColumnOfTheSampleHasAField(): void
    {
        $header = $this->sampleHeader();
        self::assertNotEmpty($header);

        foreach (array_keys($header) as $columnIndex) {
            self::assertNotNull(
                $this->preSelectedField($columnIndex),
                sprintf('column %d ("%s") has no field to map onto', $columnIndex, $header[$columnIndex])
            );
        }
    }

    public function testTheSampleEndsOnTheLastField(): void
    {
        $header = $this->sampleHeader();
        $lastColumn = count($header) - 1;

        // If the sample carried surplus columns the tail would slide, which is how "Advanced stock
        // management" came to pre-select the accessories field.
        self::assertSame('accessories', $this->preSelectedField($lastColumn));
    }

    public function testEveryRowHasAsManyValuesAsTheHeader(): void
    {
        $expected = count($this->sampleHeader());
        $handle = fopen(self::SAMPLE, 'r');
        $line = 0;

        try {
            while (false !== ($row = fgetcsv($handle, 0, ';', '"', ''))) {
                self::assertCount($expected, $row, sprintf('line %d has the wrong number of values', $line));
                ++$line;
            }
        } finally {
            fclose($handle);
        }

        self::assertGreaterThan(1, $line, 'the sample must carry example rows');
    }

    /**
     * @return string[]
     */
    private function sampleHeader(): array
    {
        $handle = fopen(self::SAMPLE, 'r');

        try {
            return fgetcsv($handle, 0, ';', '"', '');
        } finally {
            fclose($handle);
        }
    }

    /**
     * Mirrors AdminImportController::getTypeValuesOptions().
     */
    private function preSelectedField(int $columnIndex): ?string
    {
        $index = 0;

        foreach (array_keys($this->availableFields) as $key) {
            if ('price_tin' === $key) {
                ++$columnIndex;
            }
            if ($index === $columnIndex + 1 && !in_array($key, ['price_tin', 'feature'], true)) {
                return $key;
            }
            ++$index;
        }

        return null;
    }
}
