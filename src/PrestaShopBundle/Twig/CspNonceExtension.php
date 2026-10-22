<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Exposes the per-request Content Security Policy nonce (generated on
 * kernel.request by CspHeaderSubscriber and stored in the main request
 * attributes) to Twig templates, so inline scripts can declare
 * `nonce="{{ csp_nonce() }}"` and stay valid once the policy becomes enforcing.
 */
final class CspNonceExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('csp_nonce', [$this, 'getNonce']),
        ];
    }

    public function getNonce(): string
    {
        $request = $this->requestStack->getMainRequest();
        if ($request === null) {
            return '';
        }

        $nonce = $request->attributes->get('csp_nonce');

        return is_string($nonce) ? $nonce : '';
    }
}
