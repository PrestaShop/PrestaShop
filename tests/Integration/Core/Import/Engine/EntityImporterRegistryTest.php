<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ProductImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\UnknownEntityTypeException;
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

        $fields = $importer->getFields();
        $this->assertCount(66, iterator_to_array($fields));
        $this->assertSame(['name'], $fields->getRequiredFields());

        $phases = $importer->getPhases();
        $this->assertSame(['validation', 'database', 'association'], array_map(static fn ($phase) => $phase->id, $phases));
        $this->assertTrue($phases[0]->pausing);
        $this->assertFalse($phases[1]->pausing);
    }
}
