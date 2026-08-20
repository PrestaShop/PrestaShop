<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\InvalidResumeCursorException;
use ReflectionClass;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use TypeError;

/**
 * Pure-logic coverage of ProductRowImporter helpers — the importer itself is
 * exercised end to end by tests/Integration/Core/Import/Engine/.
 */
class ProductRowImporterTest extends TestCase
{
    /**
     * @dataProvider providesRowValues
     *
     * @param array<string, string> $row
     */
    public function testHasValueOnlySkipsUnmappedAndBlankCells(array $row, bool $expected): void
    {
        $this->assertSame($expected, $this->invokeProtected($this->buildImporter(), 'hasValue', [$row, 'field']));
    }

    /**
     * @return iterable<string, array{0: array<string, string>, 1: bool}>
     */
    public static function providesRowValues(): iterable
    {
        yield 'unmapped column' => [[], false];
        yield 'blank cell' => [['field' => ''], false];
        // the reason hasValue() must not be !empty(): "0" disables a boolean
        // field or carries a zero price/dimension — it is a real value
        yield 'zero' => [['field' => '0'], true];
        yield 'plain value' => [['field' => 'value'], true];
        yield 'whitespace' => [['field' => ' '], true];
    }

    /**
     * @dataProvider providesRowFailures
     */
    public function testOnlyDomainAndEngineExceptionMessagesReachTheUser(Throwable $failure, bool $messageIsExposed): void
    {
        $message = (string) $this->invokeProtected($this->buildImporter(), 'buildRowFailureMessage', [$failure]);

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
     * The helpers under test only touch the translator, so the 16-dependency
     * constructor is bypassed and a pass-through translator is injected.
     */
    private function buildImporter(): ProductRowImporter
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id, array $parameters = []): string => strtr($id, $parameters)
        );

        $reflection = new ReflectionClass(ProductRowImporter::class);
        $importer = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('translator');
        $property->setValue($importer, $translator);

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
