<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Context;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

class ContextTest extends TestCase
{
    /**
     * When no currency is set on the context (e.g. PDF generation on a multishop
     * install), getComputingPrecision() must not fatal on the strictly typed
     * ComputingPrecision::getPrecision(int), but fall back to a default precision.
     */
    public function testGetComputingPrecisionFallsBackToDefaultWhenCurrencyIsNull(): void
    {
        $context = new Context();
        $context->currency = null;

        $this->assertSame(Precision::DEFAULT_PRECISION, $context->getComputingPrecision());
    }
}
