<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\Exception\UnknownPhaseException;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;

/**
 * Contract for entity importers driven by the import engine phase model.
 *
 * An importer declares an ordered list of phases (validation, database,
 * association, ... — ids are open strings) and processes them batch by batch.
 * Importers dispatch existing CQRS commands for persistence and must never
 * mutate the run context: progress is reported through PhaseBatchResult and
 * applied by the caller (the batch sequencer).
 *
 * Every autoconfigured service implementing this interface is automatically
 * tagged with EntityImporterRegistry::SERVICE_TAG (registered in
 * PrestaShopExtension) and collected into the registry — this also applies
 * to module services, as long as their definitions enable autoconfiguration.
 */
interface EntityImporterInterface
{
    /**
     * Engine-wide convention for multi-value association fields: a cell
     * containing exactly this marker empties the association (legacy could
     * not clear associations at all).
     */
    public const CLEAR_ASSOCIATION_MARKER = '@clear@';

    /**
     * String identifier of the imported entity (e.g. 'product').
     */
    public function getEntityType(): string;

    /**
     * Translated human-readable label of the imported entity (e.g. 'Products'),
     * displayed in the import page entity dropdown.
     */
    public function getLabel(): string;

    /**
     * Importable fields, embedded in the importer (EntityField value objects).
     */
    public function getFields(): EntityFieldCollectionInterface;

    /**
     * Ordered phase list. Ids are open strings; the ImportPhaseDefinition::PHASE_*
     * constants only name the common conventions.
     *
     * @return list<ImportPhaseDefinition>
     */
    public function getPhases(): array;

    /**
     * Total unit count for a phase, computed once at phase entry (the caller
     * stores it on the context, see ImportRunContext::enterPhase()); 0 means
     * the phase is skipped.
     *
     * @throws UnknownPhaseException when the phase id is not one of getPhases()
     */
    public function countPhaseUnits(string $phaseId, ImportRunContext $context): int;

    /**
     * Processes up to $limit units from the phase's current position (row position
     * + opaque resume cursor carried by the context).
     *
     * @throws UnknownPhaseException when the phase id is not one of getPhases()
     */
    public function processPhaseBatch(string $phaseId, ImportRunContext $context, int $limit): PhaseBatchResult;
}
