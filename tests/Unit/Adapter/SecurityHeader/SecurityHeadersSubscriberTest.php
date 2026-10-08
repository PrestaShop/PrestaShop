<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\SecurityHeader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersProvider;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersSubscriber;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The subscriber copies whatever SecurityHeadersProvider yields onto a main-request response. The
 * provider is final, so the real one is used over mocked interface collaborators; the logger catch
 * branch mirrors CspHeaderSubscriber and is left to the end-to-end path (it needs the legacy static).
 */
class SecurityHeadersSubscriberTest extends TestCase
{
    /** The three low-risk headers the provider ships on by default. */
    private const DEFAULTS = [
        'PS_SEC_NOSNIFF' => '1',
        'PS_SEC_FRAME_OPTIONS' => 'SAMEORIGIN',
        'PS_SEC_REFERRER_POLICY' => 'strict-origin-when-cross-origin',
        'PS_SEC_HSTS' => '0',
        'PS_SEC_HSTS_MAX_AGE' => '15552000',
        'PS_SEC_HSTS_SUBDOMAINS' => '0',
        'PS_SEC_HSTS_PRELOAD' => '0',
        'PS_SEC_PERMISSIONS_POLICY' => '',
    ];

    public function testItSubscribesToTheResponseEvent(): void
    {
        $this->assertSame([KernelEvents::RESPONSE => 'onKernelResponse'], SecurityHeadersSubscriber::getSubscribedEvents());
    }

    public function testItCopiesTheProviderHeadersOntoAMainRequestResponse(): void
    {
        $response = new Response();

        $this->subscriber(self::DEFAULTS)->onKernelResponse(
            $this->event(HttpKernelInterface::MAIN_REQUEST, $response)
        );

        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
    }

    public function testItIgnoresSubRequestsAndSetsNoHeader(): void
    {
        $response = new Response();

        $this->subscriber(self::DEFAULTS)->onKernelResponse(
            $this->event(HttpKernelInterface::SUB_REQUEST, $response)
        );

        $this->assertFalse($response->headers->has('X-Content-Type-Options'));
    }

    public function testItSetsNothingWhenTheFeatureFlagIsOff(): void
    {
        $response = new Response();

        $this->subscriber(self::DEFAULTS, flagEnabled: false)->onKernelResponse(
            $this->event(HttpKernelInterface::MAIN_REQUEST, $response)
        );

        $this->assertFalse($response->headers->has('X-Content-Type-Options'));
        $this->assertFalse($response->headers->has('X-Frame-Options'));
    }

    public function testItForwardsTheRequestSchemeSoHstsIsHttpsOnly(): void
    {
        $config = ['PS_SEC_HSTS' => '1'] + self::DEFAULTS;

        $secure = new Response();
        $this->subscriber($config)->onKernelResponse(
            $this->event(HttpKernelInterface::MAIN_REQUEST, $secure, Request::create('https://shop.test/'))
        );
        $this->assertSame('max-age=15552000', $secure->headers->get('Strict-Transport-Security'));

        $plain = new Response();
        $this->subscriber($config)->onKernelResponse(
            $this->event(HttpKernelInterface::MAIN_REQUEST, $plain, Request::create('http://shop.test/'))
        );
        $this->assertFalse($plain->headers->has('Strict-Transport-Security'));
    }

    public function testTheBackOfficeScopeReadsTheAllShopsValueAndTheStorefrontTheCurrentContext(): void
    {
        // The back office is a single global surface; the storefront reads the shop being served
        // (null constraint = current context).
        $this->assertEquals(ShopConstraint::allShops(), $this->scopeReadWith(allShopsScope: true));
        $this->assertNull($this->scopeReadWith(allShopsScope: false));
    }

    /** Drives the subscriber once and returns the ShopConstraint its reads were scoped with. */
    private function scopeReadWith(bool $allShopsScope): ?ShopConstraint
    {
        $seen = [];
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            function (string $key, $default = null, ?ShopConstraint $shopConstraint = null) use (&$seen) {
                $seen[] = $shopConstraint;

                return self::DEFAULTS[$key] ?? $default;
            }
        );

        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn(true);

        $subscriber = new SecurityHeadersSubscriber(new SecurityHeadersProvider($configuration, $flagChecker), $allShopsScope);
        $subscriber->onKernelResponse($this->event(HttpKernelInterface::MAIN_REQUEST, new Response()));

        $this->assertNotEmpty($seen, 'The provider must read the configuration at least once');
        foreach ($seen as $shopConstraint) {
            $this->assertEquals($seen[0], $shopConstraint, 'Every read must use the same scope');
        }

        return $seen[0];
    }

    /**
     * @param array<string, string> $config
     */
    private function subscriber(array $config, bool $flagEnabled = true): SecurityHeadersSubscriber
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(fn (string $key) => $config[$key] ?? null);

        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn($flagEnabled);

        return new SecurityHeadersSubscriber(new SecurityHeadersProvider($configuration, $flagChecker));
    }

    private function event(int $requestType, Response $response, ?Request $request = null): ResponseEvent
    {
        return new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request ?? new Request(),
            $requestType,
            $response
        );
    }
}
