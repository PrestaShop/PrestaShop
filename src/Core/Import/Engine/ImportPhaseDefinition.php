<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

/**
 * Definition of one import phase, declared in code by the importer and never
 * persisted (the database only ever stores phase id strings).
 *
 * Ids are open strings: the PHASE_* constants name the four common conventions,
 * but importers may declare custom phases (e.g. an attribute-generation
 * pre-phase for combinations).
 *
 * A pausing phase that completes with at least one message stops the run as
 * awaiting_confirmation so the client can review; a clean pausing phase
 * continues without pause.
 */
class ImportPhaseDefinition
{
    public const PHASE_VALIDATION = 'validation';
    public const PHASE_DATABASE = 'database';
    public const PHASE_ASSOCIATION = 'association';
    public const PHASE_FINALIZATION = 'finalization';

    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly bool $pausing = false,
    ) {
    }
}
