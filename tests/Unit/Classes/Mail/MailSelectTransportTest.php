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
use stdClass;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * Modules answer actionMailSelectTransport with a transport or an explicit false. Hook::exec turns a module's
 * exception into an empty string, so anything that is neither must fail the send rather than fall back to
 * the native transport, and two modules both claiming a mail must not pick one at random.
 */
class MailSelectTransportTest extends TestCase
{
    public function testNoModuleOrOnlyDeclinesKeepTheNativeTransport(): void
    {
        $this->assertNull(self::pick([]));
        $this->assertNull(self::pick(['module_a' => false, 'module_b' => false]));
    }

    public function testTheOnlyTransportReturnedIsUsed(): void
    {
        $transport = $this->createMock(TransportInterface::class);

        $this->assertSame($transport, self::pick(['module_a' => false, 'module_b' => $transport]));
    }

    /**
     * @dataProvider invalidAnswers
     *
     * @param mixed $results
     */
    public function testAnythingElseFailsTheSend($results, string $message): void
    {
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage($message);

        self::pick($results);
    }

    public static function invalidAnswers(): iterable
    {
        yield 'a module that threw, as Hook::exec reports it' => [['module_a' => ''], 'Invalid custom mail transport.'];
        yield 'a module that returned nothing' => [['module_a' => null], 'Invalid custom mail transport.'];
        yield 'a module that returned true' => [['module_a' => true], 'Invalid custom mail transport.'];
        yield 'something other than a transport' => [['module_a' => new stdClass()], 'Invalid custom mail transport.'];
        yield 'results that are not a list' => ['', 'Invalid mail transport selection results.'];
    }

    public function testTwoModulesClaimingTheMailFailTheSend(): void
    {
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Multiple custom mail transports selected.');

        self::pick([
            'module_a' => $this->createMock(TransportInterface::class),
            'module_b' => $this->createMock(TransportInterface::class),
        ]);
    }

    public function testTheFailureIsCaughtWhereMailCatchesMailerErrors(): void
    {
        // Mail::send logs and returns false, and Mail::sendMailTest returns the message, for this interface.
        $this->assertInstanceOf(ExceptionInterface::class, new TransportException('x'));
    }

    /**
     * @param mixed $results
     */
    private static function pick($results): ?TransportInterface
    {
        return (new ReflectionMethod(Mail::class, 'pickSelectedTransport'))->invoke(null, $results);
    }
}
