<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspPolicyProvider;

class CspPolicyProviderTest extends TestCase
{
    public function testItReturnsTheBaseCollectionPolicy(): void
    {
        $directives = (new CspPolicyProvider())->getPolicy(1)->getDirectives();

        $this->assertSame(
            [
                'default-src' => ["'self'"],
                'img-src' => ["'self'", 'data:'],
                'font-src' => ["'self'", 'data:'],
            ],
            $directives
        );
    }
}
