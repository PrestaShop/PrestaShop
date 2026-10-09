<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Db;

use DbPDO;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * A shop whose database is on another host had no way to require TLS for it. The paths are now
 * defines, read from the environment, and they turn into PDO options here.
 *
 * A shop that configures none of them must connect exactly as before, which is what most of these
 * cases pin.
 */
class DbPDOSslOptionsTest extends TestCase
{
    public function testNothingConfiguredAddsNoOptions(): void
    {
        $this->assertSame([], $this->options('', '', ''));
    }

    public function testTheAuthorityAloneIsEnough(): void
    {
        $this->assertSame([PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/ca.pem'], $this->options('/etc/ssl/ca.pem', '', ''));
    }

    public function testAClientCertificateIsPassedWithItsKey(): void
    {
        $this->assertSame(
            [
                PDO::MYSQL_ATTR_SSL_CA => '/ca.pem',
                PDO::MYSQL_ATTR_SSL_CERT => '/client.pem',
                PDO::MYSQL_ATTR_SSL_KEY => '/client.key',
            ],
            $this->options('/ca.pem', '/client.pem', '/client.key')
        );
    }

    public function testAnEmptyPathIsNotPassedAsAnEmptyOption(): void
    {
        // An empty string reaching PDO as an SSL path is a connection error, not "no TLS".
        $options = $this->options('/ca.pem', '', '');

        $this->assertArrayNotHasKey(PDO::MYSQL_ATTR_SSL_CERT, $options);
        $this->assertArrayNotHasKey(PDO::MYSQL_ATTR_SSL_KEY, $options);
    }

    public function testVerificationIsOnlySentWhenItIsBeingTurnedOff(): void
    {
        $verifying = $this->options('/ca.pem', '', '', true);
        $notVerifying = $this->options('/ca.pem', '', '', false);

        $this->assertArrayNotHasKey(PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT, $verifying);
        $this->assertFalse($notVerifying[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]);
    }

    public function testVerificationIsNotSentWithoutAnyTlsMaterial(): void
    {
        // Nothing to verify against, so a shop that only sets the flag connects as it always did.
        $this->assertSame([], $this->options('', '', '', false));
    }

    public function testBothConstantFamiliesProduceTheSameOptions(): void
    {
        // The method has two branches only because PHP 8.5 deprecated the PDO:: prefixed constants;
        // the Pdo\Mysql ones are aliases of the same integers, which is what makes the branch safe.
        // Exercising both branches pins that, without naming either class here: PHP resolves class
        // names case insensitively but the autoloader does not, and the constants live in a polyfill
        // below PHP 8.4, so a literal reference in a test is a portability problem of its own.
        $this->assertSame(
            $this->options('/ca.pem', '/client.pem', '/client.key', true, false),
            $this->options('/ca.pem', '/client.pem', '/client.key', true, true)
        );
    }

    /**
     * @return array<int, string>
     */
    private function options(
        string $ca,
        string $cert,
        string $key,
        bool $verify = true,
        bool $modern = false
    ): array {
        $method = new ReflectionMethod(DbPDO::class, 'getSslOptions');
        $method->setAccessible(true);

        return $method->invoke(null, $ca, $cert, $key, $verify, $modern);
    }
}
