<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Security\NativeAdminSessionReader;
use SessionHandlerInterface;
use SessionUpdateTimestampHandlerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

/**
 * @runTestsInSeparateProcesses
 *
 * @preserveGlobalState disabled
 */
final class NativeAdminSessionReaderTest extends TestCase
{
    public function testReadDoesNotWriteOrRenewAndPreservesFrontOfficeBags(): void
    {
        ini_set('session.serialize_handler', 'php_serialize');
        ini_set('session.use_cookies', '1');
        $snapshot = ['_sf2_attributes' => ['_security_main' => 'server-token'], '_sf2_meta' => ['u' => time() - 100]];
        $handler = $this->createHandler(serialize($snapshot));
        $frontSession = new Session(new PhpBridgeSessionStorage($handler));
        $frontSession->set('customer_marker', 'preserved');
        $previousSession = $_SESSION;
        $previousId = session_id();
        $previousHeaders = headers_list();
        $previousOptions = $this->getSessionOptions();

        $_COOKIE[session_name()] = 'existing-session';
        $reader = new NativeAdminSessionReader();
        self::assertSame($snapshot, $reader->read());
        self::assertSame($snapshot, $reader->read());

        self::assertSame($previousSession, $_SESSION);
        self::assertSame('preserved', $frontSession->get('customer_marker'));
        $frontSession->set('after_read', 'still-connected');
        self::assertSame('still-connected', $_SESSION['_sf2_attributes']['after_read']);
        self::assertSame($previousId, session_id());
        self::assertSame(PHP_SESSION_NONE, session_status());
        self::assertSame($previousHeaders, headers_list());
        self::assertSame($previousOptions, $this->getSessionOptions());
        self::assertSame(0, $handler->writes);
        self::assertSame(0, $handler->updates);
        self::assertSame(0, $handler->deletions);
        self::assertSame(0, $handler->collections);
    }

    public function testMissingAndInvalidCookieDoNotOpenSession(): void
    {
        $handler = $this->createHandler('');
        session_set_save_handler($handler, false);
        $reader = new NativeAdminSessionReader();
        self::assertNull($reader->read());
        $_COOKIE[session_name()] = '../invalid';
        self::assertNull($reader->read());
        self::assertSame(0, $handler->reads);
        self::assertSame(0, $handler->writes);
    }

    public function testUnknownSessionIsNotAcceptedOrWritten(): void
    {
        ini_set('session.serialize_handler', 'php_serialize');
        $handler = $this->createHandler('');
        session_set_save_handler($handler, false);
        $reader = new NativeAdminSessionReader();
        $_COOKIE[session_name()] = 'unknown-session';
        self::assertNull($reader->read());
        self::assertSame(0, $handler->writes);
        self::assertSame(0, $handler->updates);
        self::assertSame(PHP_SESSION_NONE, session_status());
    }

    private function getSessionOptions(): array
    {
        return array_map(static fn (string $name): string|false => ini_get('session.' . $name), [
            'cache_limiter', 'gc_probability', 'use_cookies', 'use_strict_mode', 'use_trans_sid',
        ]);
    }

    private function createHandler(string $data): CountingSessionHandler
    {
        return new CountingSessionHandler($data);
    }
}

final class CountingSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    public int $reads = 0;
    public int $writes = 0;
    public int $updates = 0;
    public int $deletions = 0;
    public int $collections = 0;

    public function __construct(private readonly string $data)
    {
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        ++$this->reads;

        return $id === 'existing-session' ? $this->data : '';
    }

    public function write(string $id, string $data): bool
    {
        ++$this->writes;

        return true;
    }

    public function destroy(string $id): bool
    {
        ++$this->deletions;

        return true;
    }

    public function gc(int $max_lifetime): int
    {
        ++$this->collections;

        return 0;
    }

    public function validateId(string $id): bool
    {
        return $id === 'existing-session';
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        ++$this->updates;

        return true;
    }
}
