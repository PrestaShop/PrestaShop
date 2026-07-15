<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidImapServer;
use PrestaShop\PrestaShop\Core\ConstraintValidator\ValidImapServerValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class ValidImapServerValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ValidImapServerValidator
    {
        return new ValidImapServerValidator();
    }

    /**
     * @dataProvider provideValidServers
     */
    public function testValidServer(?string $server): void
    {
        $this->validator->validate($server, new ValidImapServer());

        $this->assertNoViolation();
    }

    public static function provideValidServers(): iterable
    {
        yield 'null' => [null];
        yield 'empty' => [''];
        yield 'hostname' => ['mail.example.com'];
        yield 'IP address' => ['192.0.2.1'];
        // A hyphen in the middle of a word (not preceded by whitespace or
        // start-of-string) is a legitimate hostname/user character and must
        // not be flagged.
        yield 'hyphenated hostname' => ['mail-server.example.com'];
        yield 'username with mid-word hyphen' => ['jane-doe@example.com'];
    }

    /**
     * @dataProvider provideInvalidServers
     */
    public function testProxyCommandOptionIsRejected(string $server): void
    {
        $constraint = new ValidImapServer(message: 'Invalid IMAP server');

        $this->validator->validate($server, $constraint);

        $this->buildViolation('Invalid IMAP server')
            ->assertRaised()
        ;
    }

    public static function provideInvalidServers(): iterable
    {
        yield 'lowercase substring' => ['mail.example.com/oProxyCommand=payload'];
        yield 'mixed case substring' => ['mail.example.com/OPROXYCOMMAND=payload'];
        // Real-world CVE-2018-19518-class PoCs inject via the *user* argument
        // to imap_open(), typically as a leading "-o..." flag consumed by the
        // rsh/ssh preauth shell-out, with or without a space after "-o".
        yield 'leading flag, no space' => ['-oProxyCommand=touch /tmp/pwned'];
        yield 'leading flag, with space' => ['-o ProxyCommand=touch /tmp/pwned'];
        yield 'flag after whitespace' => ['someuser -oProxyCommand=touch /tmp/pwned'];
        yield 'other dangerous -o flag' => ['-oPermitLocalCommand=yes'];
        // "{"/"}" could terminate or extend the "{host:port/options}" string.
        yield 'brace injection' => ['mail.example.com}/pop3{evil'];
        // Control characters (e.g. embedded newline) are rejected outright.
        yield 'embedded newline' => ["mail.example.com\nEvilHeader: x"];
    }
}
