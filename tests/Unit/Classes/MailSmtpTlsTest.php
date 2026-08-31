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
     *
     * @param bool|string|null $setting
     */
    public function testTheEncryptionSettingIsReadAsABoolean($setting, bool $expected, string $because): void
    {
        self::assertSame($expected, self::useImplicitTls($setting), $because);
    }

    public static function getEncryptionSettings(): array
    {
        return [
            'the TLS setting asks for encryption' => ['tls', true, 'the merchant enabled encryption'],
            'no encryption stays plain' => ['off', false, 'the merchant asked for no encryption'],
            'an unset value is treated as off' => [false, false, 'nothing configured means nothing forced'],
            'an empty value is treated as off' => ['', false, 'an empty setting is not a request for TLS'],
            'the setting is case insensitive' => ['OFF', false, 'the value is stored as typed'],
        ];
    }

    /**
     * @dataProvider getPortsAndSettings
     *
     * @param bool|string $setting
     */
    public function testTheTransportPicksImplicitTlsByPort(
        $setting,
        int $port,
        bool $expectedTls,
        string $because
    ): void {
        $transport = new EsmtpTransport('smtp.example.com', $port, self::esmtpTransportParameter($setting));

        $getStream = new ReflectionMethod($transport, 'getStream');
        $getStream->setAccessible(true);
        /** @var SocketStream $socket */
        $socket = $getStream->invoke($transport);

        self::assertSame($expectedTls, $socket->isTLS(), $because);
    }

    public static function getPortsAndSettings(): array
    {
        return [
            'the submission port connects plain and upgrades with STARTTLS' => [
                'tls', 587, false, 'implicit TLS on 587 is what the report describes as failing',
            ],
            'the SMTPS port keeps connecting with implicit TLS' => [
                'tls', 465, true, 'shops already working on 465 must keep working',
            ],
            'encryption off refuses implicit TLS on the submission port' => [
                'off', 587, false, 'no encryption was asked for',
            ],
            'encryption off refuses implicit TLS even on the SMTPS port' => [
                'off', 465, false, 'the port must not re-enable what the merchant turned off',
            ],
        ];
    }

    /**
     * Mirrors the two call sites in Mail: an encrypted setting leaves the choice to the transport
     * rather than forcing implicit TLS on. Passing the boolean straight through is the regression.
     *
     * @param bool|string|null $smtpEncryption
     */
    private static function esmtpTransportParameter($smtpEncryption): ?bool
    {
        return self::useImplicitTls($smtpEncryption) ? null : false;
    }

    /**
     * @param bool|string|null $smtpEncryption
     */
    private static function useImplicitTls($smtpEncryption): bool
    {
        $method = new ReflectionMethod(Mail::class, 'useImplicitTls');
        $method->setAccessible(true);

        return $method->invoke(null, $smtpEncryption);
    }
}
