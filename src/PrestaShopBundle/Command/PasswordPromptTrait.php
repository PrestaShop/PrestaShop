<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use RuntimeException;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Shared interactive password prompt for the prestashop:employee:* CLI commands.
 *
 * Asks for a password twice (hidden), validates both inputs and either returns
 * the confirmed value or throws a RuntimeException describing precisely why
 * the input was rejected. Throwing — rather than returning null — keeps the
 * "why" distinguishable when more validation rules are added later (length,
 * policy, …).
 */
trait PasswordPromptTrait
{
    /**
     * @throws RuntimeException when the password is empty or the two prompts do not match
     */
    private function askPasswordTwice(SymfonyStyle $io, string $label = 'Password'): string
    {
        $first = $this->askHidden($io, $label);
        if ($first === '') {
            throw new RuntimeException('Password cannot be empty.');
        }

        $second = $this->askHidden($io, sprintf('Confirm %s', lcfirst($label)));
        if ($first !== $second) {
            throw new RuntimeException('Passwords do not match.');
        }

        return $first;
    }

    private function askHidden(SymfonyStyle $io, string $label): string
    {
        $question = new Question($label);
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        return (string) $io->askQuestion($question);
    }
}
