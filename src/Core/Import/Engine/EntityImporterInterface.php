<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\Exception\UnknownPhaseException;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Contract for entity importers driven by the import engine phase model.
 *
 * An importer declares an ordered list of phases (validation, database,
 * association, ... — ids are open strings) and processes them batch by batch.
 * Importers dispatch existing CQRS commands for persistence and must never
 * mutate the run context: progress is reported through PhaseBatchResult and
 * applied by the caller (the batch sequencer).
 *
 * Implementations registered as autoconfigured services are automatically
 * tagged and collected into the EntityImporterRegistry — this also applies to
 * module services, as long as their definitions enable autoconfiguration.
 */
#[AutoconfigureTag(EntityImporterRegistry::SERVICE_TAG)]
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
     * Total unit count for a phase, recomputed at phase entry; 0 means the phase is skipped.
     *
     * @throws UnknownPhaseException when the phase is not one of getPhases()
     */
    public function countPhaseUnits(ImportPhaseDefinition $phase, ImportRunContext $context): int;

    /**
     * Processes up to $limit units from the phase's current position (row position
     * + opaque resume cursor carried by the context).
     *
     * @throws UnknownPhaseException when the phase is not one of getPhases()
     */
    public function processPhaseBatch(ImportPhaseDefinition $phase, ImportRunContext $context, int $limit): PhaseBatchResult;
}
