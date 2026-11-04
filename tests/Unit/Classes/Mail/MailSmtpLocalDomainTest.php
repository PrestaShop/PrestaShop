<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Mail;

use Mail;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

/**
 * Symfony Mailer greets the SMTP server with EHLO [127.0.0.1] unless told otherwise, which
 * anti-spam filters (cPanel among others) reject: https://github.com/PrestaShop/PrestaShop/issues/40595
 */
class MailSmtpLocalDomainTest extends TestCase
{
    private $serverName;

    protected function setUp(): void
    {
        $this->serverName = $_SERVER['SERVER_NAME'] ?? null;
    }

    protected function tearDown(): void
    {
        if (null === $this->serverName) {
            unset($_SERVER['SERVER_NAME']);
        } else {
            $_SERVER['SERVER_NAME'] = $this->serverName;
        }
    }

    /**
     * @dataProvider candidates
     */
    public function testTheFirstUsableNameIsPicked(array $candidates, ?string $expected): void
    {
        $method = new ReflectionMethod(Mail::class, 'getSmtpLocalDomain');

        $this->assertSame($expected, $method->invoke(null, $candidates));
    }

    public static function candidates(): iterable
    {
        yield 'the server name' => [['shop.example.com', 'other.example.com'], 'shop.example.com'];
        yield 'normalized' => [[' Shop.Example.COM '], 'shop.example.com'];
        yield 'a port is dropped' => [['shop.example.com:8080'], 'shop.example.com'];
        yield 'no server name in CLI, the shop domain is next' => [[null, 'shop.example.com', 'host'], 'shop.example.com'];
        yield 'localhost is what we are getting away from' => [['localhost', 'shop.example.com'], 'shop.example.com'];
        yield 'a loopback address too' => [['127.0.0.1', '[::1]', 'shop.example.com'], 'shop.example.com'];
        yield 'a public address is fine' => [['93.184.216.34'], '93.184.216.34'];
        yield 'an IPv6 address keeps its colons' => [['2a01:4f8::1'], '2a01:4f8::1'];
        yield 'not a host name' => [['bad host/name', 'shop.example.com'], 'shop.example.com'];
        yield 'the machine host name as last resort' => [[null, '', 'web01'], 'web01'];
        yield 'nothing usable keeps the Symfony default' => [[null, '', false], null];
    }

    public function testTheTransportGreetsWithTheServerName(): void
    {
        $_SERVER['SERVER_NAME'] = 'shop.example.com';
        $transport = new EsmtpTransport('smtp.example.com', 587);

        (new ReflectionMethod(Mail::class, 'setSmtpLocalDomain'))->invoke(null, $transport, 'other.example.com');

        $this->assertSame('shop.example.com', $transport->getLocalDomain());
    }

    public function testWithoutServerNameTheShopDomainIsUsed(): void
    {
        unset($_SERVER['SERVER_NAME']);
        $transport = new EsmtpTransport('smtp.example.com', 587);

        (new ReflectionMethod(Mail::class, 'setSmtpLocalDomain'))->invoke(null, $transport, 'shop.example.com');

        $this->assertSame('shop.example.com', $transport->getLocalDomain());
    }
}
