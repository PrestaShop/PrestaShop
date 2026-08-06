<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\RowMapper;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Import\File\ResumableFileReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * End-of-validation in-memory sub-step: builds a hashed identity set from
 * the file's identity columns (id, reference) in one scan, then verifies
 * every accessories target against the set and the database in a second
 * scan. Nothing is persisted; misses are warnings (the link will be dropped
 * at the association phase). Skippable per run via
 * options.skipAssociationPrecheck (not recommended).
 *
 * Memory bound: roughly tens of MB per million rows (one hash-set entry per
 * identity column value).
 */
final class AccessoriesPrecheck
{
    public function __construct(
        private readonly ResumableFileReaderInterface $fileReader,
        private readonly RowMapper $rowMapper,
        private readonly ProductIdentityResolver $identityResolver,
        private readonly ValueParser $valueParser,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @return list<ImportMessage>
     */
    public function run(ImportRunContext $context): array
    {
        $identitySet = $this->buildIdentitySet($context);

        $messages = [];
        $rowIndex = -1;
        foreach ($this->fileReader->readFrom($context->getWorkingFile()) as $record) {
            ++$rowIndex;
            if ($context->isRowSkipped($rowIndex)) {
                continue;
            }

            $row = $this->rowMapper->map($record, $context);
            $accessories = $row['accessories'] ?? '';
            if ('' === $accessories || EntityImporterInterface::CLEAR_ASSOCIATION_MARKER === $accessories) {
                continue;
            }

            foreach ($this->valueParser->split($accessories, $context->getMultipleValueSeparator()) as $target) {
                $messages = array_merge($messages, $this->checkTarget($target, $identitySet, $rowIndex, $context));
            }
        }

        return $messages;
    }

    /**
     * @return array<string, true> keys: 'id:<value>' and 'ref:<value>'
     */
    private function buildIdentitySet(ImportRunContext $context): array
    {
        $identitySet = [];
        $collectIds = $context->getOptions()->forceIds && $context->isFieldMapped('id');
        $collectReferences = $context->isFieldMapped('reference');

        if (!$collectIds && !$collectReferences) {
            return $identitySet;
        }

        $rowIndex = -1;
        foreach ($this->fileReader->readFrom($context->getWorkingFile()) as $record) {
            ++$rowIndex;
            if ($context->isRowSkipped($rowIndex)) {
                continue;
            }

            $row = $this->rowMapper->map($record, $context);
            if ($collectIds && '' !== ($row['id'] ?? '')) {
                $identitySet['id:' . $row['id']] = true;
            }
            if ($collectReferences && '' !== ($row['reference'] ?? '')) {
                $identitySet['ref:' . $row['reference']] = true;
            }
        }

        return $identitySet;
    }

    /**
     * A NUMERIC target is treated as a product id first, but may equally be
     * a reference that merely looks numeric: an id match wins (with a warning
     * when a reference also matches), a reference match is the fallback (also
     * warned, so the user can double-check the intent).
     *
     * @param array<string, true> $identitySet
     *
     * @return list<ImportMessage>
     */
    private function checkTarget(string $target, array $identitySet, int $rowIndex, ImportRunContext $context): array
    {
        if (ctype_digit($target)) {
            $probe = $this->identityResolver->classifyNumericTarget((int) $target, $context->getShopId());
            $idMatches = isset($identitySet['id:' . $target]) || $probe['idExists'];
            $referenceMatches = isset($identitySet['ref:' . $target]) || null !== $probe['referenceMatchId'];

            if ($idMatches && $referenceMatches) {
                return [$this->warning($rowIndex, $this->translator->trans('Accessory "%target%" matches both a product id and a product reference; it will be linked by id.', ['%target%' => $target], 'Admin.Advparameters.Notification'))];
            }
            if ($idMatches) {
                return [];
            }
            if ($referenceMatches) {
                return [$this->warning($rowIndex, $this->translator->trans('Accessory "%target%" matches no product id; it will be linked by reference.', ['%target%' => $target], 'Admin.Advparameters.Notification'))];
            }
        } elseif (isset($identitySet['ref:' . $target])
            || null !== $this->identityResolver->findExistingByReferenceThenId($target, null, $context->getShopId())) {
            return [];
        }

        return [$this->warning($rowIndex, $this->translator->trans('Accessory "%target%" matches no product in the file or the catalog; the link will be dropped.', ['%target%' => $target], 'Admin.Advparameters.Notification'))];
    }

    private function warning(int $rowIndex, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, $message, $rowIndex, 'accessories');
    }
}
