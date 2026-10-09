<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier;

use Order;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Carrier\TrackingUrlFormatter;

class TrackingUrlFormatterTest extends TestCase
{
    /**
     * @dataProvider provideTrackingUrls
     */
    public function testFormat(?string $trackingUrl, ?string $trackingNumber, string $expected): void
    {
        $order = $this->createMock(Order::class);
        $order->id = 42;
        $order->reference = 'XKBK NABJK';

        $this->assertSame($expected, TrackingUrlFormatter::format($trackingUrl, $trackingNumber, $order));
    }

    public static function provideTrackingUrls(): iterable
    {
        yield 'no url' => [null, '1Z999', ''];
        yield 'no placeholder' => ['https://example.com/track', '1Z999', 'https://example.com/track'];
        yield 'tracking number is inserted as is' => ['https://example.com/track/@', '1Z 999', 'https://example.com/track/1Z 999'];
        yield 'no tracking number' => ['https://example.com/track/@', null, 'https://example.com/track/'];
        yield 'order placeholders' => [
            'https://example.com/track?num=@&id={order_id}&ref={order_reference}',
            '1Z999',
            'https://example.com/track?num=1Z999&id=42&ref=XKBK%20NABJK',
        ];
        yield 'values are not replaced again' => ['https://example.com/track/@/{order_id}', '{order_id}@', 'https://example.com/track/{order_id}@/42'];
    }

    public function testFillWithSampleValues(): void
    {
        $this->assertSame(
            'https://example.com/1/track/@?id=1&ref=1&zip=1&name={lastname}',
            TrackingUrlFormatter::fillWithSampleValues('https://example.com/{country_iso}/track/@?id={order_id}&ref={order_reference}&zip={postcode}&name={lastname}')
        );
    }
}
