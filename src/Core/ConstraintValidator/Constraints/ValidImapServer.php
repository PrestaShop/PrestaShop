<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\ValidImapServerValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Protects the IMAP mailbox connection string from command injection options.
 */
final class ValidImapServer extends Constraint
{
    public string $message = 'The IMAP URL is invalid.';

    public function __construct(?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        if (null !== $message) {
            $this->message = $message;
        }
    }

    public function validatedBy(): string
    {
        return ValidImapServerValidator::class;
    }
}
