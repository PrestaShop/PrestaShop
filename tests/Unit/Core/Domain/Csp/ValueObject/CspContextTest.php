<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Csp\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;

/**
 * The surface discriminator stored on every CSP rule and log row. Its string values are persisted
 * (column default 'front'), so they must stay stable, and only the storefront is scoped per shop.
 */
class CspContextTest extends TestCase
{
    public function testItsPersistedValuesAreStable(): void
    {
        $this->assertSame('front', CspContext::FRONT->value);
        $this->assertSame('admin', CspContext::ADMIN->value);
    }

    public function testOnlyTheStorefrontIsScopedPerShop(): void
    {
        $this->assertTrue(CspContext::FRONT->isPerShop());
        $this->assertFalse(CspContext::ADMIN->isPerShop(), 'The back office is a single global surface');
    }
}
