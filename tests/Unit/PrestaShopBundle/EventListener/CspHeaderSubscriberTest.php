<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\EventListener\CspHeaderSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CspHeaderSubscriberTest extends TestCase
{
    private const HEADER_NAME = 'Content-Security-Policy-Report-Only';

    public function testGetSubscribedEvents(): void
    {
        $events = CspHeaderSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
        $this->assertArrayHasKey(KernelEvents::RESPONSE, $events);
        $this->assertSame('onKernelResponse', $events[KernelEvents::RESPONSE]);
    }

    public function testItSetsReportOnlyHeaderWithNonceOnMainRequest(): void
    {
        $request = Request::create('https://example.com/admin/');
        $response = new Response();
        $subscriber = new CspHeaderSubscriber();

        $subscriber->onKernelRequest($this->createRequestEvent($request));
        $subscriber->onKernelResponse($this->createResponseEvent($request, $response));

        $header = $response->headers->get(self::HEADER_NAME);
        $nonce = (string) $request->attributes->get(CspHeaderSubscriber::NONCE_ATTRIBUTE);

        $this->assertNotSame('', $nonce);
        $this->assertStringContainsString("script-src 'self' 'nonce-" . $nonce . "'", $header);
        $this->assertStringContainsString("default-src 'self'", $header);
    }

    public function testNonceDiffersBetweenRequests(): void
    {
        $subscriber = new CspHeaderSubscriber();
        $nonces = [];

        for ($i = 0; $i < 2; ++$i) {
            $request = Request::create('https://example.com/admin/');
            $response = new Response();

            $subscriber->onKernelRequest($this->createRequestEvent($request));
            $subscriber->onKernelResponse($this->createResponseEvent($request, $response));

            $nonces[] = (string) $request->attributes->get(CspHeaderSubscriber::NONCE_ATTRIBUTE);
        }

        $this->assertNotSame($nonces[0], $nonces[1]);
    }

    public function testItDoesNothingOnSubRequests(): void
    {
        $request = Request::create('https://example.com/admin/');
        $response = new Response();
        $subscriber = new CspHeaderSubscriber();

        $event = new ResponseEvent(
            $this->createKernelMock(),
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $response
        );

        $subscriber->onKernelResponse($event);

        $this->assertFalse($response->headers->has(self::HEADER_NAME));
        $this->assertFalse($request->attributes->has(CspHeaderSubscriber::NONCE_ATTRIBUTE));
    }

    public function testItSkipsHeaderWhenNonceIsMissing(): void
    {
        $request = Request::create('https://example.com/admin/');
        $response = new Response();
        $subscriber = new CspHeaderSubscriber();

        $subscriber->onKernelResponse($this->createResponseEvent($request, $response));

        $this->assertFalse($response->headers->has(self::HEADER_NAME));
    }

    private function createRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(
            $this->createKernelMock(),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }

    private function createResponseEvent(Request $request, Response $response): ResponseEvent
    {
        return new ResponseEvent(
            $this->createKernelMock(),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );
    }

    private function createKernelMock(): HttpKernelInterface
    {
        return $this->getMockBuilder(HttpKernelInterface::class)->getMock();
    }
}
