<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Csp\CspSurfaceResolver;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The one place that decides the CSP surface. It must read the surface from the query (?context=admin,
 * the grid URLs) and from the route attributes (the admin bulk-revoke route carries it there), or the
 * grids and the controller would disagree on that route.
 */
class CspSurfaceResolverTest extends TestCase
{
    public function testTheQueryStringSelectsTheAdminSurface(): void
    {
        $this->assertSame(CspContext::ADMIN, CspSurfaceResolver::fromRequest(new Request(['context' => 'admin'])));
    }

    public function testARouteAttributeSelectsTheAdminSurface(): void
    {
        // The admin bulk-revoke route has no query string; it carries the surface as a route default.
        $this->assertSame(CspContext::ADMIN, CspSurfaceResolver::fromRequest(new Request([], [], ['context' => 'admin'])));
    }

    public function testItDefaultsToTheStorefront(): void
    {
        $this->assertSame(CspContext::FRONT, CspSurfaceResolver::fromRequest(new Request()));
        $this->assertSame(CspContext::FRONT, CspSurfaceResolver::fromRequest(new Request(['context' => 'front'])));
        $this->assertSame(CspContext::FRONT, CspSurfaceResolver::fromRequest(new Request(['context' => 'anything-else'])));
    }

    public function testAnEmptyRequestStackIsTheStorefront(): void
    {
        $this->assertSame(CspContext::FRONT, CspSurfaceResolver::fromRequestStack(new RequestStack()));
    }

    public function testItResolvesAndFlagsAdminFromTheRequestStack(): void
    {
        $stack = new RequestStack();
        $stack->push(new Request([], [], ['context' => 'admin']));

        $this->assertSame(CspContext::ADMIN, CspSurfaceResolver::fromRequestStack($stack));
        $this->assertTrue(CspSurfaceResolver::isAdminRequest($stack));
    }

    public function testIsAdminRequestIsFalseForTheStorefront(): void
    {
        $stack = new RequestStack();
        $stack->push(new Request());

        $this->assertFalse(CspSurfaceResolver::isAdminRequest($stack));
    }
}
