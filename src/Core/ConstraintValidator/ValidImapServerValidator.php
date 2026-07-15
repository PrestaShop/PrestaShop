<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidImapServer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ValidImapServerValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidImapServer) {
            throw new UnexpectedTypeException($constraint, ValidImapServer::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (self::containsSuspiciousPattern($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }

    /**
     * True when the value could inject a command-line flag or extend/escape
     * the `{host:port/options}` IMAP connection string built in
     * `SyncCustomerServiceImapMailboxHandler`. `imap_open()` passes the host
     * and user unescaped to the underlying c-client library, which can shell
     * out to `rsh`/`ssh` for some preauthentication schemes; a value
     * starting with `-` (or containing `-` right after whitespace) is read
     * as a command-line flag by that shell-out — the primitive behind
     * CVE-2018-19518-class `imap_open()` RCEs, e.g. `-oProxyCommand=...`.
     * `{`/`}` and control characters are rejected too since they can
     * terminate or extend the connection string itself. The plain
     * substring check is kept alongside as a defense-in-depth backstop for
     * that specific known option name.
     *
     * Called both by this Form-time validator and, directly, by the sync
     * handler right before it builds the connection string — so a value
     * set through any path other than this form (direct DB write, module,
     * future API endpoint) is still checked before it reaches `imap_open()`.
     */
    public static function containsSuspiciousPattern(string $value): bool
    {
        if (false !== stripos($value, 'oProxyCommand')) {
            return true;
        }

        return 1 === preg_match('/[\x00-\x1F\x7F{}]|(?:^|\s)-/', $value);
    }
}
