<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ProductImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\DuplicateEntityTypeException;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\UnknownEntityTypeException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityImporterRegistryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testProductImporterIsRegistered(): void
    {
        $registry = self::getContainer()->get(EntityImporterRegistry::class);

        $this->assertTrue($registry->has(ProductImporter::ENTITY_TYPE));
        $this->assertInstanceOf(ProductImporter::class, $registry->get(ProductImporter::ENTITY_TYPE));
        $this->assertArrayHasKey(ProductImporter::ENTITY_TYPE, $registry->all());
    }

    public function testUnknownEntityTypeThrows(): void
    {
        $registry = self::getContainer()->get(EntityImporterRegistry::class);

        $this->assertFalse($registry->has('unknown_entity'));
        $this->expectException(UnknownEntityTypeException::class);
        $registry->get('unknown_entity');
    }

    public function testProductImporterExposesFieldsAndPhases(): void
    {
        $registry = self::getContainer()->get(EntityImporterRegistry::class);
        $importer = $registry->get(ProductImporter::ENTITY_TYPE);

        $this->assertSame('Products', $importer->getLabel());

        $fields = $importer->getFields();
        $this->assertCount(67, iterator_to_array($fields));
        // no required COLUMN: a product name is only mandatory when the row
        // creates a product, which the row validator decides per row, so an
        // update-only file (match_ref + reference + price) stays importable
        $this->assertSame([], $fields->getRequiredFields());

        $phases = $importer->getPhases();
        $this->assertSame(['validation', 'database', 'association_validation', 'association'], array_map(static fn ($phase) => $phase->id, $phases));
        $this->assertTrue($phases[0]->pausing);
        $this->assertFalse($phases[1]->pausing);
        $this->assertTrue($phases[2]->pausing);
        $this->assertFalse($phases[3]->pausing);
    }

    public function testDuplicateEntityTypeThrows(): void
    {
        $registry = new EntityImporterRegistry([
            $this->buildStubImporter('duplicated_type'),
            $this->buildStubImporter('duplicated_type'),
        ]);

        $this->expectException(DuplicateEntityTypeException::class);
        $this->expectExceptionMessage('duplicated_type');
        $registry->all();
    }

    private function buildStubImporter(string $entityType): EntityImporterInterface
    {
        return new class($entityType) implements EntityImporterInterface {
            public function __construct(private readonly string $entityType)
            {
            }

            public function getEntityType(): string
            {
                return $this->entityType;
            }

            public function getLabel(): string
            {
                return 'Stub importer';
            }

            public function getFields(): EntityFieldCollectionInterface
            {
                return EntityFieldCollection::createFromArray([]);
            }

            public function getPhases(): array
            {
                return [];
            }

            public function countPhaseUnits(string $phaseId, ImportRunContext $context): int
            {
                return 0;
            }

            public function processPhaseBatch(string $phaseId, ImportRunContext $context, int $limit): PhaseBatchResult
            {
                return new PhaseBatchResult(0, [], [], null);
            }
        };
    }
}
