<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\FoundEntity;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\ProductFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step\ProductRowStepInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\InvalidResumeCursorException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use TypeError;

/**
 * Orchestration and pure-logic coverage of ProductRowImporter (step loop with
 * stub steps, identity short-circuits, catch-all semantics) — the real steps
 * are exercised end to end by tests/Integration/Core/Import/Engine/.
 */
class ProductRowImporterTest extends TestCase
{
    private const ROW_INDEX = 7;
    private const EXISTING_PRODUCT_ID = 42;
    private const LANGUAGE_ID = 1;

    public function testStepsRunInOrderWithTheResolvedRowValues(): void
    {
        $calls = [];
        $importer = $this->buildImporter([
            $this->buildStep(function (...$arguments) use (&$calls): array {
                $calls[] = ['first', $arguments];

                return [$this->warning('from first')];
            }),
            $this->buildStep(function (...$arguments) use (&$calls): array {
                $calls[] = ['second', $arguments];

                return [$this->warning('from second')];
            }),
        ]);
        $context = $this->buildContext();

        $messages = $importer->importRow(['reference' => 'REF-1'], self::ROW_INDEX, $context);

        $this->assertSame(['from first', 'from second'], array_map(static fn (ImportMessage $message): string => $message->message, $messages));
        $this->assertSame(['first', 'second'], array_column($calls, 0));
        foreach ($calls as [, $arguments]) {
            [$row, $rowIndex, $productId, $isCreation, $languageId, $stepContext] = $arguments;
            $this->assertSame(['reference' => 'REF-1'], $row);
            $this->assertSame(self::ROW_INDEX, $rowIndex);
            $this->assertSame(self::EXISTING_PRODUCT_ID, $productId);
            $this->assertFalse($isCreation, 'The finder matched an existing product');
            $this->assertSame(self::LANGUAGE_ID, $languageId);
            $this->assertSame($context, $stepContext);
        }
    }

    public function testAnUnsupportedStepIsNeverApplied(): void
    {
        $importer = $this->buildImporter([
            $this->buildStep(
                static fn (): array => throw new RuntimeException('apply() must not be called when supports() is false'),
                supports: false
            ),
        ]);

        $this->assertSame([], $importer->importRow([], self::ROW_INDEX, $this->buildContext()));
    }

    /**
     * Pins the accepted divergence from the pre-split implementation: steps
     * report by RETURNING messages, so a throwing step loses its own earlier
     * messages, while messages from the steps that completed before it
     * survive next to the row error.
     */
    public function testAThrowingStepFailsTheRowKeepingCompletedStepsMessages(): void
    {
        $thirdStepRan = false;
        $importer = $this->buildImporter([
            $this->buildStep(fn (): array => [$this->warning('completed step')]),
            $this->buildStep(static fn (): array => throw new RuntimeException('SQLSTATE[23000]: boom')),
            $this->buildStep(static function () use (&$thirdStepRan): array {
                $thirdStepRan = true;

                return [];
            }),
        ]);

        $messages = $importer->importRow([], self::ROW_INDEX, $this->buildContext());

        $this->assertFalse($thirdStepRan, 'Steps after the throwing one must be skipped');
        $this->assertCount(2, $messages);
        $this->assertSame('completed step', $messages[0]->message);
        $this->assertSame(ImportMessage::SEVERITY_ERROR, $messages[1]->severity);
        $this->assertSame([self::ROW_INDEX], $messages[1]->rows);
        $this->assertStringContainsString('unexpected error', $messages[1]->message);
        $this->assertStringNotContainsString('SQLSTATE', $messages[1]->message, 'Implementation detail stays in the log');
    }

    public function testAnAmbiguousReferenceFailsTheRowBeforeAnyStepRuns(): void
    {
        $importer = $this->buildImporter(
            [$this->buildStep(static fn (): array => throw new RuntimeException('no step may run on an identity failure'))],
            new FoundEntity([
                ['id' => 42, 'matchedBy' => FoundEntity::MATCHED_BY_REFERENCE],
                ['id' => 43, 'matchedBy' => FoundEntity::MATCHED_BY_REFERENCE],
            ])
        );

        $messages = $importer->importRow(['reference' => 'DUP'], self::ROW_INDEX, $this->buildContext());

        $this->assertCount(1, $messages);
        $this->assertSame(ImportMessage::SEVERITY_ERROR, $messages[0]->severity);
        $this->assertSame(ImportPhaseDefinition::PHASE_DATABASE, $messages[0]->phase);
        $this->assertSame('reference', $messages[0]->field);
        $this->assertSame([self::ROW_INDEX], $messages[0]->rows);
    }

    /**
     * @dataProvider providesRowFailures
     */
    public function testOnlyDomainAndEngineExceptionMessagesReachTheUser(Throwable $failure, bool $messageIsExposed): void
    {
        $message = (string) $this->invokeProtected($this->buildBareImporter(), 'buildRowFailureMessage', [$failure]);

        if ($messageIsExposed) {
            $this->assertStringContainsString($failure->getMessage(), $message);
        } else {
            $this->assertStringNotContainsString($failure->getMessage(), $message);
            $this->assertStringContainsString('unexpected error', $message);
        }
    }

    /**
     * @return iterable<string, array{0: Throwable, 1: bool}>
     */
    public static function providesRowFailures(): iterable
    {
        yield 'domain exception' => [
            new SpecificPriceConstraintException('Identical specific price already exists for product "12"'),
            true,
        ];
        yield 'import engine exception' => [
            new InvalidResumeCursorException('The persisted resume cursor "abc" cannot be interpreted'),
            true,
        ];
        // implementation detail (table names, constraint names, stack context)
        // belongs in the log, never in a back-office notification
        yield 'runtime exception' => [
            new RuntimeException('SQLSTATE[23000]: Integrity constraint violation on ps_specific_price'),
            false,
        ];
        yield 'type error' => [
            new TypeError('Argument #1 ($productId) must be of type int, string given'),
            false,
        ];
    }

    /**
     * @param list<ProductRowStepInterface> $steps
     */
    private function buildImporter(array $steps, ?FoundEntity $match = null): ProductRowImporter
    {
        $language = $this->createMock(LanguageInterface::class);
        $language->method('getId')->willReturn(self::LANGUAGE_ID);
        $languageRepository = $this->createMock(LanguageRepositoryInterface::class);
        $languageRepository->method('getOneByIsoCode')->willReturn($language);

        $productFinder = $this->createMock(ProductFinder::class);
        $productFinder->method('findRowMatch')->willReturn(
            $match ?? new FoundEntity([['id' => self::EXISTING_PRODUCT_ID, 'matchedBy' => FoundEntity::MATCHED_BY_ID]])
        );

        return new ProductRowImporter(
            $steps,
            $this->createMock(CommandBusInterface::class),
            new ValueParser(),
            $productFinder,
            $this->createMock(ProductRepository::class),
            $languageRepository,
            $this->createMock(Tools::class),
            $this->buildTranslator(),
            $this->createMock(LoggerInterface::class),
        );
    }

    private function buildStep(callable $apply, bool $supports = true): ProductRowStepInterface
    {
        return new class($apply, $supports) implements ProductRowStepInterface {
            public function __construct(
                private $apply,
                private readonly bool $supportsRow,
            ) {
            }

            public function supports(array $row): bool
            {
                return $this->supportsRow;
            }

            public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
            {
                return ($this->apply)($row, $rowIndex, $productId, $isCreation, $languageId, $context);
            }
        };
    }

    private function buildContext(): ImportRunContext
    {
        return new ImportRunContext(
            'product',
            '/tmp/working-file.csv',
            10,
            'en',
            ',',
            [],
            ImportRunOptions::fromArray([]),
            ShopConstraint::shop(1)
        );
    }

    private function warning(string $text): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_DATABASE, $text, [self::ROW_INDEX]);
    }

    private function buildTranslator(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id, array $parameters = []): string => strtr($id, $parameters)
        );

        return $translator;
    }

    /**
     * The failure-message helper only touches the translator, so the
     * constructor is bypassed and a pass-through translator is injected.
     */
    private function buildBareImporter(): ProductRowImporter
    {
        $reflection = new ReflectionClass(ProductRowImporter::class);
        $importer = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('translator');
        $property->setValue($importer, $this->buildTranslator());

        return $importer;
    }

    /**
     * @param list<mixed> $arguments
     */
    private function invokeProtected(ProductRowImporter $importer, string $method, array $arguments): mixed
    {
        return (new ReflectionClass(ProductRowImporter::class))->getMethod($method)->invoke($importer, ...$arguments);
    }
}
