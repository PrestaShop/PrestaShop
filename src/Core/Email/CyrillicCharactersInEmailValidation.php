<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Email;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Result\InvalidEmail;
use Egulias\EmailValidator\Result\Reason\ExceptionFound;
use Egulias\EmailValidator\Validation\EmailValidation;
use PrestaShop\PrestaShop\Core\Exception\NonASCIIInLocalPartException;

class CyrillicCharactersInEmailValidation implements EmailValidation
{
    /**
     * @var InvalidEmail|null
     */
    private $error;

    /**
     * {@inheritdoc}
     */
    public function isValid(string $email, EmailLexer $emailLexer): bool
    {
        $parts = explode('@', $email);
        if (preg_match('/[^\x00-\x7F]/', $parts[0])) {
            $this->error = new InvalidEmail(new ExceptionFound(new NonASCIIInLocalPartException()), '');
        }

        return null === $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getError(): ?InvalidEmail
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getWarnings(): array
    {
        return [];
    }
}
