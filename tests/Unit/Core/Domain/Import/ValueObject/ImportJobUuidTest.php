<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Import\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobUuid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

class ImportJobUuidTest extends TestCase
{
    public function testItAcceptsAWellFormedUuid(): void
    {
        $uuid = new ImportJobUuid('0198f1a4-0b3c-7c21-9a4e-1f2b3c4d5e6f');

        $this->assertSame('0198f1a4-0b3c-7c21-9a4e-1f2b3c4d5e6f', $uuid->getValue());
    }

    /**
     * RFC 4122 hex digits are case-insensitive on input; the lock key, the working file name and
     * the primary key are all derived from the value, so one uuid must have one spelling.
     */
    public function testItCanonicalisesTheSpelling(): void
    {
        $uuid = new ImportJobUuid('0198F1A4-0B3C-7C21-9A4E-1F2B3C4D5E6F');

        $this->assertSame('0198f1a4-0b3c-7c21-9a4e-1f2b3c4d5e6f', $uuid->getValue());
    }

    /**
     * @dataProvider provideMalformedValues
     */
    public function testItRefusesAnythingThatIsNotAUuid(string $value): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_ID);

        new ImportJobUuid($value);
    }

    public static function provideMalformedValues(): Generator
    {
        yield 'empty' => [''];
        yield 'not a uuid at all' => ['import-job-1'];
        yield 'sequential id' => ['42'];
        yield 'truncated' => ['0198f1a4-0b3c-7c21-9a4e'];
        yield 'non hexadecimal' => ['0198f1a4-0b3c-7c21-9a4e-1f2b3c4d5ezz'];
    }

    /**
     * Pinned because v7's ascending ids are only free to choose while the table has no rows.
     */
    public function testItGeneratesTimeOrderedIdentifiers(): void
    {
        $first = ImportJobUuid::generate();
        $second = ImportJobUuid::generate();

        $this->assertInstanceOf(UuidV7::class, Uuid::fromString($first->getValue()));
        $this->assertNotSame($first->getValue(), $second->getValue());
        $this->assertLessThan(
            0,
            strcmp($first->getValue(), $second->getValue()),
            'A later uuid must sort after an earlier one'
        );
    }

    public function testAGeneratedIdentifierIsAcceptedBackByTheValueObject(): void
    {
        $generated = ImportJobUuid::generate();

        $this->assertSame($generated->getValue(), (new ImportJobUuid($generated->getValue()))->getValue());
    }
}
