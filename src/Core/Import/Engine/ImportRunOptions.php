<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

/**
 * Frozen per-run options, mirroring the future ImportRun entity "options" JSON
 * column without depending on Doctrine.
 *
 * truncate, sendEmail and dryRun are consumed by the batch sequencer (PR2),
 * not by importers: truncate executes once at database-phase entry, dryRun
 * truncates the phase list after validation (API validate-only).
 */
class ImportRunOptions
{
    public function __construct(
        public readonly bool $truncate = false,
        public readonly bool $forceIds = false,
        public readonly bool $matchRef = false,
        public readonly bool $regenerate = false,
        public readonly bool $sendEmail = false,
        public readonly bool $dryRun = false,
    ) {
    }

    /**
     * @param array<string, bool> $options
     */
    public static function fromArray(array $options): self
    {
        return new self(
            truncate: (bool) ($options['truncate'] ?? false),
            forceIds: (bool) ($options['forceIds'] ?? false),
            matchRef: (bool) ($options['matchRef'] ?? false),
            regenerate: (bool) ($options['regenerate'] ?? false),
            sendEmail: (bool) ($options['sendEmail'] ?? false),
            dryRun: (bool) ($options['dryRun'] ?? false),
        );
    }

    /**
     * @return array<string, bool>
     */
    public function toArray(): array
    {
        return [
            'truncate' => $this->truncate,
            'forceIds' => $this->forceIds,
            'matchRef' => $this->matchRef,
            'regenerate' => $this->regenerate,
            'sendEmail' => $this->sendEmail,
            'dryRun' => $this->dryRun,
        ];
    }
}
