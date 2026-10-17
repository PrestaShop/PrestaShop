<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Csp\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;

class CspRuleIdTest extends TestCase
{
    public function testItAcceptsAPositiveId(): void
    {
        $this->assertSame(42, (new CspRuleId(42))->getValue());
    }

    /**
     * @dataProvider provideInvalidIds
     */
    public function testItRejectsANonPositiveId(int $value): void
    {
        $this->expectException(CspConstraintException::class);
        $this->expectExceptionCode(CspConstraintException::INVALID_ID);

        new CspRuleId($value);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public function provideInvalidIds(): iterable
    {
        yield 'zero' => [0];
        yield 'negative' => [-1];
    }
}
