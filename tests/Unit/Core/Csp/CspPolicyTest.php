<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Csp\CspPolicy;

class CspPolicyTest extends TestCase
{
    public function testItStartsEmpty(): void
    {
        $this->assertSame([], (new CspPolicy())->getDirectives());
    }

    public function testItKeepsSourcesGroupedByDirectiveInInsertionOrder(): void
    {
        $policy = new CspPolicy();
        $policy->addSource('script-src', "'self'");
        $policy->addSource('script-src', 'https://cdn.example.com');
        $policy->addSource('img-src', 'data:');

        $this->assertSame(
            [
                'script-src' => ["'self'", 'https://cdn.example.com'],
                'img-src' => ['data:'],
            ],
            $policy->getDirectives()
        );
    }

    public function testItDeduplicatesTheSameSourceWithinADirective(): void
    {
        $policy = new CspPolicy();
        $policy->addSource('script-src', "'self'");
        $policy->addSource('script-src', "'self'");

        $this->assertSame(['script-src' => ["'self'"]], $policy->getDirectives());
    }

    public function testAddingARealSourceDropsAnExclusiveNone(): void
    {
        // The base seeds object-src 'none'; widening it must not emit the malformed `'none' https://x`.
        $policy = new CspPolicy();
        $policy->addSource('object-src', "'none'");
        $policy->addSource('object-src', 'https://cdn.example.com');

        $this->assertSame(['object-src' => ['https://cdn.example.com']], $policy->getDirectives());
    }

    public function testNoneIsNotAddedNextToExistingSources(): void
    {
        $policy = new CspPolicy();
        $policy->addSource('object-src', 'https://cdn.example.com');
        $policy->addSource('object-src', "'none'");

        $this->assertSame(['object-src' => ['https://cdn.example.com']], $policy->getDirectives());
    }

    public function testItCoarsensGranularScriptAndStyleDirectivesToTheirParent(): void
    {
        // A granular directive would override its parent without inheriting the base 'self', so any
        // contribution (curated rule, theme, module hook) is reduced to the coarse parent directive.
        $policy = new CspPolicy();
        $policy->addSource('script-src-elem', 'https://cdn.example.com');
        $policy->addSource('script-src-attr', "'unsafe-inline'");
        $policy->addSource('style-src-elem', 'https://styles.example.com');

        $this->assertSame(
            [
                'script-src' => ['https://cdn.example.com', "'unsafe-inline'"],
                'style-src' => ['https://styles.example.com'],
            ],
            $policy->getDirectives()
        );
    }
}
