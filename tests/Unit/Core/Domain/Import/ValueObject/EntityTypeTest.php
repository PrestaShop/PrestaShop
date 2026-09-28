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
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\EntityType;

class EntityTypeTest extends TestCase
{
    /**
     * @dataProvider provideAcceptedTypes
     */
    public function testItAcceptsAnImporterDeclaredType(string $value): void
    {
        $this->assertSame($value, (new EntityType($value))->getValue());
    }

    public static function provideAcceptedTypes(): Generator
    {
        yield 'the core product importer' => ['product'];
        yield 'a module importer' => ['demo_note'];
        yield 'digits after the first character' => ['ean13_alias'];
        yield 'exactly the column length' => [str_repeat('a', 64)];
    }

    /**
     * @dataProvider provideRejectedTypes
     */
    public function testItRefusesAMalformedType(string $value): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_ENTITY_TYPE);

        new EntityType($value);
    }

    public static function provideRejectedTypes(): Generator
    {
        yield 'empty' => [''];
        yield 'capitalised' => ['Product'];
        yield 'camel case' => ['demoNote'];
        yield 'kebab case' => ['demo-note'];
        yield 'leading digit' => ['1product'];
        yield 'namespace separator' => ['module\\product'];
        yield 'path traversal' => ['../product'];
        yield 'one character over the column length' => [str_repeat('a', 65)];
    }

    /**
     * A job whose importer was uninstalled must still load, to report why it cannot continue.
     */
    public function testItDoesNotCheckThatAnImporterExists(): void
    {
        $this->assertSame('no_importer_declares_this', (new EntityType('no_importer_declares_this'))->getValue());
    }
}
