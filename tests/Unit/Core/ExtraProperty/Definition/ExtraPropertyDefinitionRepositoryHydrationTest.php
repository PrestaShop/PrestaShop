<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Definition;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepository;
use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Hydrating the registry must never let one bad row take a page or an endpoint down: the
 * repository feeds every request (Admin API responses, BO grids and forms, front-office reads),
 * so a row that cannot be turned into a definition — data drift, a downgrade, a value written
 * straight into the table — is skipped and logged once, while its valid siblings stay served.
 *
 * This is also the read-side half of the value-object validation: a row carrying what a
 * registration would have refused (a "<" in a label, a foreign translation domain on a
 * module-owned definition…) is not rendered — it is dropped here.
 */
class ExtraPropertyDefinitionRepositoryHydrationTest extends TestCase
{
    private const VALID_ROW = [
        'id_extra_property_definition' => 7,
        'entity_name' => 'product',
        'module_name' => null,
        'property_name' => 'hydration_probe',
        'type' => 'string',
        'scope' => 'common',
        'label_wording' => 'Hydration probe',
    ];

    public function testAValidRowIsHydrated(): void
    {
        $logger = new HydrationRecordingLogger();
        $definition = $this->repository($logger)->hydrate(self::VALID_ROW);

        $this->assertInstanceOf(ExtraPropertyDefinition::class, $definition);
        $this->assertSame('hydration_probe', $definition->getPropertyName());
        $this->assertSame([], $logger->records);
    }

    public function testAnUnreadableRowIsSkippedAndLoggedOnce(): void
    {
        $logger = new HydrationRecordingLogger();
        $repository = $this->repository($logger);
        $corrupt = ['type' => 'not_a_type'] + self::VALID_ROW;

        $this->assertNull($repository->hydrate($corrupt));
        $this->assertNull($repository->hydrate($corrupt));

        $this->assertCount(1, $logger->records, 'a persistent bad row must not flood the log');
        $this->assertSame('error', $logger->records[0]['level']);
        $this->assertStringContainsString('Skipping unreadable extra property definition row', $logger->records[0]['message']);
        $this->assertSame('7', $logger->records[0]['context']['id']);
    }

    public function testARowWhoseWordingCouldOpenATagIsSkipped(): void
    {
        $logger = new HydrationRecordingLogger();

        $this->assertNull($this->repository($logger)->hydrate(['label_wording' => '<img src=x onerror=alert(1)>'] + self::VALID_ROW));
        $this->assertCount(1, $logger->records);
        $this->assertStringContainsString('labelWording', $logger->records[0]['context']['reason']);
    }

    public function testARowWithAForeignDomainOnAModuleDefinitionIsSkipped(): void
    {
        $logger = new HydrationRecordingLogger();

        $this->assertNull($this->repository($logger)->hydrate(
            ['module_name' => 'somemodule', 'label_domain' => 'Admin.Actions'] + self::VALID_ROW
        ));
        $this->assertCount(1, $logger->records);
        $this->assertStringContainsString('Modules.', $logger->records[0]['context']['reason']);
    }

    private function repository(HydrationRecordingLogger $logger): HydratingRepository
    {
        // The connection is never used by the hydration path under test.
        return new HydratingRepository($this->createMock(Connection::class), 'ps_', $logger);
    }
}

/**
 * hydrateRowSafely() is protected: this subclass only exposes it.
 */
final class HydratingRepository extends ExtraPropertyDefinitionRepository
{
    /**
     * @param array<string, mixed> $row
     */
    public function hydrate(array $row): ?ExtraPropertyDefinition
    {
        return $this->hydrateRowSafely($row);
    }
}

final class HydrationRecordingLogger extends AbstractLogger
{
    /** @var list<array{level: string, message: string, context: array<string, mixed>}> */
    public array $records = [];

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
    }
}
