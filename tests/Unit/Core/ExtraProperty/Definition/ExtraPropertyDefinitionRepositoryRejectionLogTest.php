<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Definition;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepository;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintNormalizer;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

/**
 * Hydrating a definition whose stored constraints cannot be decoded drops them and logs the
 * rejection. The log must be written once per row content, and writing it must never re-enter the
 * hydration: the legacy logger persists through an ObjectModel, whose save loads the definitions
 * again, which decodes the same row again.
 */
class ExtraPropertyDefinitionRepositoryRejectionLogTest extends TestCase
{
    private const CORRUPT_ROW = [
        'id_extra_property_definition' => 19,
        'entity_name' => 'category',
        'module_name' => null,
        'property_name' => 'qa_code',
        'constraints' => '{{ this is not a constraint ]] ~~',
    ];

    public function testACorruptRowIsDroppedAndLoggedOnce(): void
    {
        $logger = new RecordingLogger();
        $repository = $this->repository($logger);

        $rows = $repository->decode([self::CORRUPT_ROW]);

        $this->assertNull($rows[0]['constraints']);
        $this->assertCount(1, $logger->records);
        $this->assertSame('error', $logger->records[0]['level']);
        $this->assertStringContainsString('for definition #19 (category/_core/qa_code)', $logger->records[0]['message']);
        $this->assertSame(['object_type' => 'extra_property_definition', 'object_id' => 19], $logger->records[0]['context']);
    }

    public function testTheSameRowContentIsNotReportedTwiceByTheSameInstance(): void
    {
        $logger = new RecordingLogger();
        $repository = $this->repository($logger);

        $repository->decode([self::CORRUPT_ROW]);
        $repository->decode([self::CORRUPT_ROW]);

        $this->assertCount(1, $logger->records);
    }

    public function testAChangedRowContentIsReportedAgain(): void
    {
        $logger = new RecordingLogger();
        $repository = $this->repository($logger);

        $repository->decode([self::CORRUPT_ROW]);
        $repository->decode([['constraints' => 'NotBlank, Nope(foo: 1)'] + self::CORRUPT_ROW]);

        $this->assertCount(2, $logger->records);
        $this->assertStringContainsString('Nope', $logger->records[1]['message']);
    }

    public function testValidRowsAreNotLogged(): void
    {
        $logger = new RecordingLogger();
        $repository = $this->repository($logger);

        $rows = $repository->decode([['constraints' => "NotBlank\nLength(min: 2, max: 64)"] + self::CORRUPT_ROW]);

        $this->assertCount(2, $rows[0]['constraints']);
        $this->assertSame([], $logger->records);
    }

    /**
     * The legacy logger writes a ps_log row through ObjectModel::add(), which may hydrate the
     * definitions again while the first hydration is still logging. The second pass must find the
     * rejection already reported and return instead of logging (and recursing) again.
     */
    public function testALoggerThatHydratesDefinitionsAgainDoesNotRecurse(): void
    {
        $logger = new RehydratingLogger();
        $repository = $this->repository($logger);
        $logger->repository = $repository;
        $logger->row = self::CORRUPT_ROW;

        $rows = $repository->decode([self::CORRUPT_ROW]);

        $this->assertNull($rows[0]['constraints']);
        $this->assertSame(1, $logger->reentries, 'the nested hydration must not log (and recurse) again');
        $this->assertCount(1, $logger->messages);
    }

    private function repository(LoggerInterface $logger): DecodingRepository
    {
        // The connection is never used by the decoding path under test.
        return new DecodingRepository($this->createMock(Connection::class), 'ps_', new ExtraPropertyConstraintNormalizer(), $logger);
    }
}

/**
 * enrichRowsWithDecodedConstraints() is protected: this subclass only exposes it.
 */
final class DecodingRepository extends ExtraPropertyDefinitionRepository
{
    /**
     * @param array<int, array<string, mixed>> $rows
     *
     * @return array<int, array<string, mixed>>
     */
    public function decode(array $rows): array
    {
        return $this->enrichRowsWithDecodedConstraints($rows);
    }
}

final class RecordingLogger extends AbstractLogger
{
    /** @var list<array{level: string, message: string, context: array<string, mixed>}> */
    public array $records = [];

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
    }
}

/**
 * Behaves like the legacy logger seen from the repository: every log call hydrates the definitions again.
 */
final class RehydratingLogger extends AbstractLogger
{
    /** @var list<string> */
    public array $messages = [];
    public int $reentries = 0;
    public ?DecodingRepository $repository = null;
    /** @var array<string, mixed> */
    public array $row = [];

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->messages[] = (string) $message;
        ++$this->reentries;
        $this->repository?->decode([$this->row]);
    }
}
