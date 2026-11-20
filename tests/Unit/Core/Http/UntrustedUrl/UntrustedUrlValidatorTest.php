<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Http\UntrustedUrl;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Http\UntrustedUrl\HostResolverInterface;
use PrestaShop\PrestaShop\Core\Http\UntrustedUrl\PublicIpChecker;
use PrestaShop\PrestaShop\Core\Http\UntrustedUrl\UntrustedUrlValidator;

class UntrustedUrlValidatorTest extends TestCase
{
    private const PROXY_VARIABLES = ['http_proxy', 'HTTP_PROXY', 'https_proxy', 'HTTPS_PROXY', 'all_proxy', 'ALL_PROXY'];

    /**
     * Host names used below, mapped to what the fake resolver returns for them.
     */
    private const DNS = [
        'shop.example' => ['93.184.215.14'],
        'cdn.example' => ['93.184.215.14', '93.184.215.14', '2606:2800:21f:cb07:6820:80da:af6b:8b2c'],
        'v6only.example' => ['2606:2800:21f:cb07:6820:80da:af6b:8b2c'],
        'internal.example' => ['10.0.0.5'],
        'rebind.example' => ['93.184.215.14', '127.0.0.1'],
        'metadata.example' => ['::ffff:169.254.169.254'],
        'xn--bcher-kva.example' => ['93.184.215.14'],
    ];

    /**
     * @var array<string, string|false>
     */
    private array $savedProxyVariables = [];

    /**
     * Hosts handed to the resolver, in order.
     *
     * @var string[]
     */
    private array $lookups = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (self::PROXY_VARIABLES as $variable) {
            $this->savedProxyVariables[$variable] = getenv($variable);
            putenv($variable);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->savedProxyVariables as $variable => $value) {
            putenv(false === $value ? $variable : $variable . '=' . $value);
        }
        parent::tearDown();
    }

    /**
     * Also used by ToolsTest.
     */
    public static function providerSchemes(): array
    {
        return [
            'http' => [true, 'http'],
            'https' => [true, 'https'],
            'uppercase is normalised' => [true, 'HTTPS'],
            'ftp' => [true, 'ftp'],
            'ftps' => [true, 'ftps'],
            // curl is not allowed to speak these, so they must not pass validation either.
            'sftp' => [false, 'sftp'],
            'scp' => [false, 'scp'],
            // PHP stream wrappers, the whole point of the allow-list.
            'file' => [false, 'file'],
            'phar' => [false, 'phar'],
            'data' => [false, 'data'],
            'gopher' => [false, 'gopher'],
            'zip' => [false, 'zip'],
            'empty' => [false, ''],
        ];
    }

    #[DataProvider('providerSchemes')]
    public function testIsSchemeAllowed(bool $expected, string $scheme): void
    {
        $this->assertSame($expected, $this->createValidator()->isSchemeAllowed($scheme));
    }

    /**
     * The allow-list must not accept anything the CURLPROTO_* mask does not, and every
     * scheme needs a default port to build the pin of a URL without an explicit one.
     */
    public function testAllowedSchemesAreCurlProtocolsWithADefaultPort(): void
    {
        $this->assertSame(
            ['http' => 80, 'https' => 443, 'ftp' => 21, 'ftps' => 990],
            UntrustedUrlValidator::ALLOWED_SCHEMES
        );
    }

    public static function providerAcceptedUrls(): array
    {
        return [
            'http default port' => ['shop.example:80:93.184.215.14', 'http://shop.example/img/logo.jpg'],
            'https default port' => ['shop.example:443:93.184.215.14', 'https://shop.example/img/logo.jpg'],
            'ftp default port' => ['shop.example:21:93.184.215.14', 'ftp://shop.example/file.zip'],
            'ftps default port' => ['shop.example:990:93.184.215.14', 'ftps://shop.example/file.zip'],
            'explicit port' => ['shop.example:8443:93.184.215.14', 'https://shop.example:8443/img/logo.jpg'],
            'uppercase scheme' => ['shop.example:443:93.184.215.14', 'HTTPS://shop.example/img/logo.jpg'],
            'duplicated addresses, first one pinned' => ['cdn.example:443:93.184.215.14', 'https://cdn.example/a.png'],
            'ipv6 address is bracketed' => [
                'v6only.example:443:[2606:2800:21f:cb07:6820:80da:af6b:8b2c]',
                'https://v6only.example/a.png',
            ],
            'public ipv4 literal' => ['8.8.8.8:80:8.8.8.8', 'http://8.8.8.8/a.png'],
            'public ipv6 literal' => ['2606:4700::1111:443:[2606:4700::1111]', 'https://[2606:4700::1111]/a.png'],
        ];
    }

    #[DataProvider('providerAcceptedUrls')]
    public function testGetPinnedResolveEntryPinsTheValidatedAddress(string $expected, string $url): void
    {
        $this->assertSame($expected, $this->createValidator()->getPinnedResolveEntry($url));
    }

    public static function providerRefusedUrls(): array
    {
        return [
            'loopback literal' => ['http://127.0.0.1/a.png'],
            'loopback literal with port' => ['http://127.0.0.1:8080/a.png'],
            'cloud metadata literal' => ['http://169.254.169.254/latest/meta-data/'],
            'ipv4-mapped cloud metadata literal' => ['http://[::ffff:169.254.169.254]/latest/meta-data/'],
            'ipv6 loopback literal' => ['http://[::1]/a.png'],
            'host resolving to a private address' => ['https://internal.example/a.png'],
            'host resolving to a public and a loopback address' => ['https://rebind.example/a.png'],
            'host resolving to an ipv4-mapped metadata address' => ['https://metadata.example/a.png'],
            'host that does not resolve' => ['https://unknown.example/a.png'],
            'file wrapper' => ['file:///etc/passwd'],
            'phar wrapper' => ['phar:///tmp/archive.phar/file'],
            'gopher' => ['gopher://shop.example/_payload'],
            'sftp' => ['sftp://shop.example/file.zip'],
            'scheme relative' => ['//shop.example/a.png'],
            'local path' => ['/var/www/img/a.png'],
            'no host' => ['http:///a.png'],
            'not a url' => ['http://'],
        ];
    }

    #[DataProvider('providerRefusedUrls')]
    public function testGetPinnedResolveEntryRefusesUrl(string $url): void
    {
        $this->assertNull($this->createValidator()->getPinnedResolveEntry($url));
    }

    public function testIdnHostIsResolvedAndPinnedInItsAsciiForm(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('The intl extension is required to convert IDN hosts.');
        }

        $this->assertSame(
            'xn--bcher-kva.example:443:93.184.215.14',
            $this->createValidator()->getPinnedResolveEntry('https://bücher.example/a.png')
        );
        $this->assertSame(['xn--bcher-kva.example'], $this->lookups);
    }

    public function testUrlIsValidatedButNotPinnedBehindAnOutboundProxy(): void
    {
        putenv('HTTPS_PROXY=http://proxy.example:3128');
        $validator = $this->createValidator();

        $this->assertTrue($validator->hasOutboundProxy());
        $this->assertSame('', $validator->getPinnedResolveEntry('https://shop.example/a.png'));
        // the proxy does not lift the host validation
        $this->assertNull($validator->getPinnedResolveEntry('https://internal.example/a.png'));
        $this->assertNull($validator->getPinnedResolveEntry('http://127.0.0.1/a.png'));
    }

    public function testResolvePublicIpsReturnsEveryDistinctAddress(): void
    {
        $this->assertSame(
            ['93.184.215.14', '2606:2800:21f:cb07:6820:80da:af6b:8b2c'],
            $this->createValidator()->resolvePublicIps('cdn.example')
        );
    }

    public function testResolutionsAreMemoizedRefusalsIncluded(): void
    {
        $validator = $this->createValidator();

        for ($i = 0; $i < 3; ++$i) {
            $this->assertSame(['93.184.215.14'], $validator->resolvePublicIps('shop.example'));
            $this->assertSame([], $validator->resolvePublicIps('internal.example'));
        }

        $this->assertSame(['shop.example', 'internal.example'], $this->lookups);
    }

    public function testIpLiteralsAreNotResolved(): void
    {
        $validator = $this->createValidator();

        $this->assertSame(['8.8.8.8'], $validator->resolvePublicIps('8.8.8.8'));
        $this->assertSame([], $validator->resolvePublicIps('10.0.0.1'));
        $this->assertSame([], $this->lookups);
    }

    private function createValidator(): UntrustedUrlValidator
    {
        $resolver = $this->createMock(HostResolverInterface::class);
        $resolver->method('resolve')->willReturnCallback(function (string $host): array {
            $this->lookups[] = $host;

            return self::DNS[$host] ?? [];
        });

        return new UntrustedUrlValidator(new PublicIpChecker(), $resolver);
    }
}
