<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Csp;

use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The single definition of which CSP surface a back-office request targets. The page selects the surface
 * with ?context=admin on its URLs, and the admin bulk-revoke route carries it as a `context` route default
 * instead; reading both keeps the grids, the grid definitions and the controller in step (reading only the
 * query made them disagree on the admin bulk-revoke route). Reused by all of them so the rule lives once.
 */
final class CspSurfaceResolver
{
    private function __construct()
    {
    }

    public static function fromRequest(Request $request): CspContext
    {
        $context = $request->query->get('context') ?? $request->attributes->get('context');

        return 'admin' === $context ? CspContext::ADMIN : CspContext::FRONT;
    }

    public static function fromRequestStack(RequestStack $requestStack): CspContext
    {
        $request = $requestStack->getCurrentRequest();

        return null === $request ? CspContext::FRONT : self::fromRequest($request);
    }

    public static function isAdminRequest(RequestStack $requestStack): bool
    {
        return CspContext::ADMIN === self::fromRequestStack($requestStack);
    }
}
