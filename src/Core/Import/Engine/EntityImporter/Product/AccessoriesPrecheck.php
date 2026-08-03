<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ProductLookup;
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
        private readonly ProductRowMapper $rowMapper,
        private readonly ProductLookup $productLookup,
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
        foreach ($this->fileReader->readFrom($context->getWorkingFile()) as $dataRow) {
            ++$rowIndex;
            if ($rowIndex < $context->getSkipRows() || $context->isRowSkipped($rowIndex)) {
                continue;
            }

            $row = $this->rowMapper->map($dataRow, $context);
            $accessories = $row['accessories'] ?? '';
            if ('' === $accessories || EntityImporterInterface::CLEAR_ASSOCIATION_MARKER === $accessories) {
                continue;
            }

            foreach ($this->valueParser->split($accessories, $context->getMultipleValueSeparator()) as $target) {
                if ($this->targetIsResolvable($target, $identitySet, $context)) {
                    continue;
                }

                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_VALIDATION,
                    $rowIndex,
                    'accessories',
                    $this->translator->trans('Accessory "%target%" matches no product in the file or the catalog; the link will be dropped.', ['%target%' => $target], 'Admin.Advparameters.Notification')
                );
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
        foreach ($this->fileReader->readFrom($context->getWorkingFile()) as $dataRow) {
            ++$rowIndex;
            if ($rowIndex < $context->getSkipRows() || $context->isRowSkipped($rowIndex)) {
                continue;
            }

            $row = $this->rowMapper->map($dataRow, $context);
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
     * @param array<string, true> $identitySet
     */
    private function targetIsResolvable(string $target, array $identitySet, ImportRunContext $context): bool
    {
        if (ctype_digit($target)) {
            return isset($identitySet['id:' . $target]) || $this->productLookup->productExists((int) $target);
        }

        return isset($identitySet['ref:' . $target])
            || null !== $this->productLookup->getProductIdByReference($target, $context->getShopId());
    }
}
