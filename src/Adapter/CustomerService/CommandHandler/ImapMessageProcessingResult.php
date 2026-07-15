<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

/**
 * Internal result used to distinguish terminal IMAP message outcomes from
 * transient failures that must be retried during a later synchronization.
 *
 * @internal
 */
final class ImapMessageProcessingResult
{
    private function __construct(
        private readonly bool $shouldMarkAsProcessed,
        private readonly ?string $error = null,
    ) {}

    public static function processed(?string $error = null): self
    {
        return new self(true, $error);
    }

    public static function failed(?string $error = null): self
    {
        return new self(false, $error);
    }

    public function shouldMarkAsProcessed(): bool
    {
        return $this->shouldMarkAsProcessed;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
