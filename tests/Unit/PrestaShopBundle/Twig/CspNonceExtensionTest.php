<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\CspNonceExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CspNonceExtensionTest extends TestCase
{
    public function testItReturnsNonceFromMainRequestAttributes(): void
    {
        $request = Request::create('https://example.com/admin/');
        $request->attributes->set('csp_nonce', 'abc123');

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $extension = new CspNonceExtension($requestStack);

        $this->assertSame('abc123', $extension->getNonce());
    }

    public function testItReturnsEmptyStringWhenNonceIsMissing(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://example.com/admin/'));

        $extension = new CspNonceExtension($requestStack);

        $this->assertSame('', $extension->getNonce());
    }

    public function testItReturnsEmptyStringWithoutRequest(): void
    {
        $extension = new CspNonceExtension(new RequestStack());

        $this->assertSame('', $extension->getNonce());
    }

    public function testItExposesCspNonceFunction(): void
    {
        $extension = new CspNonceExtension(new RequestStack());
        $functions = $extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertSame('csp_nonce', $functions[0]->getName());
    }
}
