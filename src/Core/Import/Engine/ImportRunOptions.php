<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

/**
 * Frozen per-run options: the typed view of the ImportRun entity's "options"
 * JSON column, and the serialization boundary for it (fromArray()/toArray()).
 *
 * It stays a separate object from ImportRunContext on purpose. The context is
 * the whole runtime state — frozen config AND the progress that moves as
 * batches run (phase, offset, cursor, skipped rows); these options are exactly
 * ONE persisted column, so keeping them apart is what lets the adapter read and
 * write that column without knowing anything else about the run.
 *
 * OPEN BY DESIGN. The typed properties are the options the CORE engine knows
 * about, but the set is not closed: any other key travels in $extra and is
 * readable through get()/has(). This matters because the context is rebuilt
 * from the database on EVERY batch request — an option that toArray() dropped
 * would silently disappear between batches, so an importer shipped by a module
 * could never receive one. Unknown keys therefore round-trip untouched.
 *
 * truncate, sendEmail and dryRun are consumed by the batch sequencer (PR2), not
 * by importers: truncate executes once at database-phase entry, dryRun truncates
 * the phase list after validation (API validate-only).
 *
 * The legacy "regenerate thumbnails" option has NO equivalent here: the CQRS
 * image pipeline always regenerates, so the flag would have no consumer (and
 * the legacy one was inverted anyway — ticking it disabled in-place
 * regeneration). The BO checkbox disappears with it.
 */
class ImportRunOptions
{
    /**
     * The keys backed by a typed property, i.e. everything NOT kept in $extra.
     */
    protected const CORE_OPTIONS = ['truncate', 'forceIds', 'matchRef', 'sendEmail', 'dryRun'];

    /**
     * @param array<string, mixed> $extra options the core engine does not know
     *                                    about, kept verbatim so importer-specific ones survive
     *                                    the request boundary between two batches
     */
    public function __construct(
        public readonly bool $truncate = false,
        public readonly bool $forceIds = false,
        public readonly bool $matchRef = false,
        public readonly bool $sendEmail = false,
        public readonly bool $dryRun = false,
        protected readonly array $extra = [],
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function fromArray(array $options): self
    {
        return new self(
            truncate: (bool) ($options['truncate'] ?? false),
            forceIds: (bool) ($options['forceIds'] ?? false),
            matchRef: (bool) ($options['matchRef'] ?? false),
            sendEmail: (bool) ($options['sendEmail'] ?? false),
            dryRun: (bool) ($options['dryRun'] ?? false),
            extra: array_diff_key($options, array_flip(static::CORE_OPTIONS)),
        );
    }

    /**
     * The complete option set, core keys first — what gets persisted back into
     * the JSON column, unknown keys included.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'truncate' => $this->truncate,
            'forceIds' => $this->forceIds,
            'matchRef' => $this->matchRef,
            'sendEmail' => $this->sendEmail,
            'dryRun' => $this->dryRun,
        ] + $this->extra;
    }

    /**
     * Any option by name, core or importer-specific — for importers that
     * declare options the engine knows nothing about. Core options are still
     * better read through their typed property.
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->toArray()[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->toArray());
    }

    /**
     * Only the importer-specific options, without the core ones.
     *
     * @return array<string, mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
