<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspHeaderBuilder;
use PrestaShop\PrestaShop\Adapter\Csp\CspHeaderSubscriber;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The guards that decide whether the FrontKernel response subscriber tries to attach a policy at
 * all. The builder is never reached on these branches, so it is instantiated without its (final)
 * dependency chain; the build/emit path itself is exercised end-to-end against a running storefront.
 */
class CspHeaderSubscriberTest extends TestCase
{
    public function testItSubscribesToTheResponseEvent(): void
    {
        $this->assertSame([KernelEvents::RESPONSE => 'onKernelResponse'], CspHeaderSubscriber::getSubscribedEvents());
    }

    public function testItIgnoresSubRequestsAndSetsNoHeader(): void
    {
        $response = new Response('x', 200, ['Content-Type' => 'text/html']);

        $this->subscriber()->onKernelResponse(
            $this->event(HttpKernelInterface::SUB_REQUEST, $response)
        );

        $this->assertNoCspHeader($response);
    }

    public function testItSkipsAnExplicitlyNonHtmlResponse(): void
    {
        $response = new Response('{}', 200, ['Content-Type' => 'application/json']);

        $this->subscriber()->onKernelResponse(
            $this->event(HttpKernelInterface::MAIN_REQUEST, $response)
        );

        $this->assertNoCspHeader($response);
    }

    public function testItSkipsRedirectResponses(): void
    {
        $response = new Response('', 302, ['Location' => '/somewhere']);

        $this->subscriber()->onKernelResponse(
            $this->event(HttpKernelInterface::MAIN_REQUEST, $response)
        );

        $this->assertNoCspHeader($response);
    }

    public function testItSkipsEmptyResponses(): void
    {
        $response = new Response('', 204);

        $this->subscriber()->onKernelResponse(
            $this->event(HttpKernelInterface::MAIN_REQUEST, $response)
        );

        $this->assertNoCspHeader($response);
    }

    private function subscriber(): CspHeaderSubscriber
    {
        // The guard branches return before touching the builder, so it never needs its real (final)
        // collaborators; reaching build() on this bare instance would error and fail the test.
        $builder = (new ReflectionClass(CspHeaderBuilder::class))->newInstanceWithoutConstructor();

        return new CspHeaderSubscriber($builder);
    }

    private function assertNoCspHeader(Response $response): void
    {
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('Content-Security-Policy-Report-Only'));
    }

    private function event(int $requestType, Response $response): ResponseEvent
    {
        return new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            $requestType,
            $response
        );
    }
}
