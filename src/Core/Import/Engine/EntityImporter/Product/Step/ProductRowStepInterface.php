<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * One per-concern unit of the database-phase product row import. The
 * orchestrator (ProductRowImporter) resolves the target product, then runs
 * every step in tag-priority order; each step owns exactly one concern (stock,
 * categories, images...) and the commands it dispatches for it.
 *
 * Registration: services implementing this interface are autoconfigured with
 * the ProductRowImporter::STEP_TAG tag (module services included, as long as
 * their definitions enable autoconfiguration) and collected by priority,
 * highest first. Core steps use explicit spaced priorities (1300 down to 100)
 * so a module step can slot between two core steps with an explicit tag
 * priority; an autoconfigured module step without one runs at priority 0,
 * after every core step. Some orderings are load-bearing — see the notes in
 * import_engine.yml before slotting between core steps.
 *
 * Contract:
 * - supports() is a row-only, side-effect-free prefilter and MAY return true
 *   in doubt; its only obligation is that false means the step is a guaranteed
 *   no-op for this row (no dispatch, no message, no DB read). Fine-grained
 *   guards that need parsing or the run context belong in apply().
 * - apply() returns its messages (never mutates shared state to report);
 *   PhaseBatchResult coalesces them per batch. A throw fails the row: the
 *   orchestrator catch-all converts it into a row ERROR, remaining steps are
 *   skipped, and the throwing step's own earlier messages are lost with it.
 *   For an auto-creation NOTICE that loss is batch-wide rather than per row:
 *   the resolvers report wasCreated on the FIRST resolution only, so when the
 *   row that created the entity throws, no later row announces it either.
 * - Step services must hold NO per-row state: one batch request may process
 *   many rows, and the PR2 sequencer may run several batches in one request.
 *   Per-batch memoization of run-invariant lookups is fine.
 *
 * This interface is an extension point WITHOUT the backward compatibility
 * promise (ADR 0017): it may evolve in a minor version, and module steps are
 * expected to adapt version by version.
 */
interface ProductRowStepInterface
{
    /**
     * @param array<string, string> $row mapped row values
     */
    public function supports(array $row): bool;

    /**
     * @param array<string, string> $row mapped row values
     * @param bool $isCreation whether the target product was created by this row (vs updating an existing one)
     * @param int $languageId id of the run's file language (single-language-file rule: on creation
     *                        localized values are duplicated into every installed language, on update
     *                        only this language is written)
     *
     * @return list<ImportMessage>
     */
    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array;
}
