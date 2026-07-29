<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Mail;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

/**
 * The third argument of EsmtpTransport selects implicit TLS (SMTPS), not STARTTLS. Forcing it on
 * for the back office "TLS" setting is what made port 587 unusable: implicit TLS only answers on
 * 465, while 587 expects a plain connection upgraded with STARTTLS.
 */
class MailSmtpTlsTest extends TestCase
{
    /**
     * @dataProvider getEncryptionSettings
     */
    public function testTheEncryptionSettingDecidesImplicitTls($setting, ?bool $expected, string $because): void
    {
        self::assertSame($expected, Mail::resolveSmtpTls($setting), $because);
    }

    public static function getEncryptionSettings(): array
    {
        return [
            'the TLS setting must not force implicit TLS' => [
                'tls', null, 'null lets the transport pick by port, which is what makes 587 work',
            ],
            'no encryption stays plain' => ['off', false, 'the merchant asked for no encryption'],
            'an unset value is treated as off' => [false, false, 'nothing configured means nothing forced'],
            'an empty value is treated as off' => ['', false, 'an empty setting is not a request for TLS'],
            'the setting is case insensitive' => ['OFF', false, 'the value is stored as typed'],
        ];
    }

    /**
     * @dataProvider getPorts
     */
    public function testTheTransportPicksImplicitTlsByPort(int $port, bool $expectedTls, string $because): void
    {
        $transport = new EsmtpTransport('smtp.example.com', $port, Mail::resolveSmtpTls('tls'));

        $getStream = new ReflectionMethod($transport, 'getStream');
        $getStream->setAccessible(true);
        /** @var SocketStream $socket */
        $socket = $getStream->invoke($transport);

        self::assertSame($expectedTls, $socket->isTLS(), $because);
    }

    public static function getPorts(): array
    {
        return [
            'the submission port connects plain and upgrades with STARTTLS' => [
                587, false, 'implicit TLS on 587 is what the report describes as failing',
            ],
            'the SMTPS port keeps connecting with implicit TLS' => [
                465, true, 'shops already working on 465 must keep working',
            ],
        ];
    }
}
