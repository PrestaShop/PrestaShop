<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\AccessoriesPrecheck;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowMapper;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowValidator;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\UnknownPhaseException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ProductLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;
use PrestaShop\PrestaShop\Core\Import\File\ResumableFileReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Product importer: validation (pausing, no writes) -> database (one CQRS
 * command dispatch chain per row) -> association (accessories, resolved
 * after every row exists so file order never matters, including mutual
 * A<->B references).
 */
final class ProductImporter implements EntityImporterInterface
{
    public const ENTITY_TYPE = 'product';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ResumableFileReaderInterface $fileReader,
        private readonly ProductRowMapper $rowMapper,
        private readonly ProductRowValidator $rowValidator,
        private readonly ProductRowImporter $rowImporter,
        private readonly AccessoriesPrecheck $accessoriesPrecheck,
        private readonly ProductLookup $productLookup,
        private readonly ValueParser $valueParser,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function getFields(): EntityFieldCollectionInterface
    {
        $fields = [
            new EntityField('id', $this->trans('ID', 'Admin.Global')),
            new EntityField('active', $this->trans('Active (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('name', $this->trans('Name', 'Admin.Global'), '', true),
            new EntityField('category', $this->trans('Categories (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('price_tex', $this->trans('Price tax excluded', 'Admin.Advparameters.Feature')),
            new EntityField('price_tin', $this->trans('Price tax included', 'Admin.Advparameters.Feature')),
            new EntityField('id_tax_rules_group', $this->trans('Tax rule ID', 'Admin.Advparameters.Feature')),
            new EntityField('wholesale_price', $this->trans('Cost price', 'Admin.Catalog.Feature')),
            new EntityField('on_sale', $this->trans('On sale (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_price', $this->trans('Discount amount', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_percent', $this->trans('Discount percent', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_from', $this->trans('Discount from (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_to', $this->trans('Discount to (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField('reference', $this->trans('Reference #', 'Admin.Advparameters.Feature')),
            new EntityField('supplier_reference', $this->trans('Supplier reference #', 'Admin.Advparameters.Feature')),
            new EntityField('supplier', $this->trans('Supplier', 'Admin.Global')),
            new EntityField('manufacturer', $this->trans('Brand', 'Admin.Global')),
            new EntityField('ean13', $this->trans('EAN-13', 'Admin.Advparameters.Feature')),
            new EntityField('isbn', $this->trans('ISBN', 'Admin.Catalog.Feature')),
            new EntityField('upc', $this->trans('UPC', 'Admin.Advparameters.Feature')),
            new EntityField('mpn', $this->trans('MPN', 'Admin.Catalog.Feature')),
            new EntityField('ecotax', $this->trans('Ecotax', 'Admin.Catalog.Feature')),
            new EntityField('width', $this->trans('Width', 'Admin.Global')),
            new EntityField('height', $this->trans('Height', 'Admin.Global')),
            new EntityField('depth', $this->trans('Depth', 'Admin.Global')),
            new EntityField('weight', $this->trans('Weight', 'Admin.Global')),
            new EntityField('delivery_in_stock', $this->trans('Delivery time of in-stock products:', 'Admin.Catalog.Feature')),
            new EntityField('delivery_out_stock', $this->trans('Delivery time of out-of-stock products with allowed orders:', 'Admin.Advparameters.Feature')),
            new EntityField('quantity', $this->trans('Quantity', 'Admin.Global')),
            new EntityField('minimal_quantity', $this->trans('Minimal quantity', 'Admin.Advparameters.Feature')),
            new EntityField('low_stock_threshold', $this->trans('Low stock level', 'Admin.Catalog.Feature')),
            new EntityField('low_stock_alert', $this->trans('Receive a low stock alert by email', 'Admin.Catalog.Feature')),
            new EntityField('location', $this->trans('Stock location', 'Admin.Catalog.Feature')),
            new EntityField('visibility', $this->trans('Visibility', 'Admin.Catalog.Feature')),
            new EntityField('additional_shipping_cost', $this->trans('Additional shipping cost', 'Admin.Advparameters.Feature')),
            new EntityField('unity', $this->trans('Unit for the price per unit', 'Admin.Advparameters.Feature')),
            new EntityField('unit_price', $this->trans('Price per unit', 'Admin.Advparameters.Feature')),
            new EntityField('description_short', $this->trans('Summary', 'Admin.Catalog.Feature')),
            new EntityField('description', $this->trans('Description', 'Admin.Global')),
            new EntityField('tags', $this->trans('Tags (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('meta_title', $this->trans('Meta title', 'Admin.Global')),
            new EntityField('meta_description', $this->trans('Meta description', 'Admin.Global')),
            new EntityField('link_rewrite', $this->trans('Rewritten URL', 'Admin.Advparameters.Feature')),
            new EntityField('available_now', $this->trans('Label when in stock', 'Admin.Catalog.Feature')),
            new EntityField('available_later', $this->trans('Label when backorder allowed', 'Admin.Advparameters.Feature')),
            new EntityField('available_for_order', $this->trans('Available for order (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('available_date', $this->trans('Product availability date', 'Admin.Advparameters.Feature')),
            new EntityField('date_add', $this->trans('Product creation date', 'Admin.Advparameters.Feature')),
            new EntityField('show_price', $this->trans('Show price (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('image', $this->trans('Image URLs (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('image_alt', $this->trans('Image alt texts (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('delete_existing_images', $this->trans('Delete existing images (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('features', $this->trans('Feature (Name:Value:Position:Customized)', 'Admin.Advparameters.Feature')),
            new EntityField('online_only', $this->trans('Available online only (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('condition', $this->trans('Condition', 'Admin.Catalog.Feature')),
            new EntityField('customizable', $this->trans('Customizable (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('uploadable_files', $this->trans('Uploadable files (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('text_fields', $this->trans('Text fields (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('out_of_stock', $this->trans('Action when out of stock', 'Admin.Advparameters.Feature')),
            new EntityField('is_virtual', $this->trans('Virtual product (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('file_url', $this->trans('File URL', 'Admin.Advparameters.Feature')),
            new EntityField(
                'nb_downloadable',
                $this->trans('Number of allowed downloads', 'Admin.Catalog.Feature'),
                $this->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', 'Admin.Catalog.Help')
            ),
            new EntityField('date_expiration', $this->trans('Expiration date (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField(
                'nb_days_accessible',
                $this->trans('Number of days', 'Admin.Advparameters.Feature'),
                $this->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', 'Admin.Catalog.Help')
            ),
            new EntityField(
                'shop',
                $this->trans('ID / Name of the store', 'Admin.Advparameters.Feature'),
                $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', 'Admin.Advparameters.Help')
            ),
            new EntityField('accessories', $this->trans('Accessories (x,y,z...)', 'Admin.Advparameters.Feature')),
        ];

        return EntityFieldCollection::createFromArray($fields);
    }

    public function getPhases(): array
    {
        return [
            new ImportPhaseDefinition(
                ImportPhaseDefinition::PHASE_VALIDATION,
                $this->trans('Validating the file', 'Admin.Advparameters.Feature'),
                true
            ),
            new ImportPhaseDefinition(
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->trans('Importing products', 'Admin.Advparameters.Feature')
            ),
            new ImportPhaseDefinition(
                ImportPhaseDefinition::PHASE_ASSOCIATION,
                $this->trans('Linking accessories', 'Admin.Advparameters.Feature')
            ),
        ];
    }

    public function countPhaseUnits(ImportPhaseDefinition $phase, ImportRunContext $context): int
    {
        $this->assertKnownPhase($phase);

        if (ImportPhaseDefinition::PHASE_ASSOCIATION === $phase->id && !$context->isFieldMapped('accessories')) {
            return 0;
        }

        return $this->countDataRows($context);
    }

    public function processPhaseBatch(ImportPhaseDefinition $phase, ImportRunContext $context, int $limit): PhaseBatchResult
    {
        $this->assertKnownPhase($phase);

        return match ($phase->id) {
            ImportPhaseDefinition::PHASE_VALIDATION => $this->processValidationBatch($context, $limit),
            ImportPhaseDefinition::PHASE_DATABASE => $this->processDatabaseBatch($context, $limit),
            default => $this->processAssociationBatch($context, $limit),
        };
    }

    private function processValidationBatch(ImportRunContext $context, int $limit): PhaseBatchResult
    {
        $messages = [];

        // one-time, file-level check at phase start
        if (null === $context->getResumeCursor() && 0 === $context->getCurrentOffset()) {
            $ownerColumnsAvailable = $context->isFieldMapped('reference')
                || ($context->getOptions()->forceIds && $context->isFieldMapped('id'));
            if ($context->isFieldMapped('accessories') && !$ownerColumnsAvailable) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_VALIDATION,
                    null,
                    'accessories',
                    $this->trans('Importing accessories requires an id column (with the force IDs option) or a reference column to identify the owning product; the accessories will be dropped.', 'Admin.Advparameters.Notification')
                );
            }
        }

        $result = $this->iterateBatch($context, $limit, function (array $row, DataRowInterface $dataRow, int $rowIndex) use ($context): array {
            if ($dataRow->isEmpty()) {
                return [
                    'messages' => [new ImportMessage(
                        ImportMessage::SEVERITY_NOTICE,
                        ImportPhaseDefinition::PHASE_VALIDATION,
                        $rowIndex,
                        null,
                        $this->trans('The row is empty and was skipped.', 'Admin.Advparameters.Notification')
                    )],
                    'skipped' => true,
                ];
            }

            $rowMessages = $this->rowValidator->validate($row, $rowIndex, $context);

            return ['messages' => $rowMessages, 'skipped' => $this->containsError($rowMessages)];
        });

        $messages = array_merge($messages, $result->messages);

        // end-of-phase association pre-check (in-memory identity set)
        if (
            $this->batchCompletesPhase($context, $result)
            && $context->isFieldMapped('accessories')
            && !$context->getOptions()->skipAssociationPrecheck
        ) {
            $messages = array_merge($messages, $this->accessoriesPrecheck->run(
                $this->contextWithBatchApplied($context, $result)
            ));
        }

        return new PhaseBatchResult($result->processedUnitCount, $messages, $result->newlySkippedRows, $result->resumeCursor);
    }

    private function processDatabaseBatch(ImportRunContext $context, int $limit): PhaseBatchResult
    {
        return $this->iterateBatch($context, $limit, function (array $row, DataRowInterface $dataRow, int $rowIndex) use ($context): array {
            if ($context->isRowSkipped($rowIndex)) {
                return ['messages' => [], 'skipped' => false];
            }

            $rowMessages = $this->rowImporter->importRow($row, $rowIndex, $context);

            return ['messages' => $rowMessages, 'skipped' => $this->containsError($rowMessages)];
        });
    }

    private function processAssociationBatch(ImportRunContext $context, int $limit): PhaseBatchResult
    {
        return $this->iterateBatch($context, $limit, function (array $row, DataRowInterface $dataRow, int $rowIndex) use ($context): array {
            if ($context->isRowSkipped($rowIndex)) {
                return ['messages' => [], 'skipped' => false];
            }

            return ['messages' => $this->associateAccessories($row, $rowIndex, $context), 'skipped' => false];
        });
    }

    /**
     * Shared batch iteration: resumes at the persisted cursor (skipping the
     * configured header rows on a fresh phase), processes up to $limit data
     * rows and reports the cursor of the last consumed row.
     *
     * @param callable(array<string, string>, DataRowInterface, int): array{messages: list<ImportMessage>, skipped: bool} $rowProcessor
     */
    private function iterateBatch(ImportRunContext $context, int $limit, callable $rowProcessor): PhaseBatchResult
    {
        $messages = [];
        $newlySkippedRows = [];
        $processed = 0;
        $cursor = $context->getResumeCursor();
        $physicalRowsToSkip = null === $cursor ? $context->getSkipRows() : 0;

        foreach ($this->fileReader->readFrom($context->getWorkingFile(), $context->getResumeCursor()) as $rowCursor => $dataRow) {
            if ($physicalRowsToSkip > 0) {
                --$physicalRowsToSkip;
                $cursor = $rowCursor;
                continue;
            }
            if ($processed >= $limit) {
                break;
            }

            $rowIndex = $context->getSkipRows() + $context->getCurrentOffset() + $processed;
            $outcome = $rowProcessor($this->rowMapper->map($dataRow, $context), $dataRow, $rowIndex);

            $messages = array_merge($messages, $outcome['messages']);
            if ($outcome['skipped']) {
                $newlySkippedRows[] = $rowIndex;
            }

            ++$processed;
            $cursor = $rowCursor;
        }

        return new PhaseBatchResult($processed, $messages, $newlySkippedRows, $cursor);
    }

    /**
     * @param array<string, string> $row
     *
     * @return list<ImportMessage>
     */
    private function associateAccessories(array $row, int $rowIndex, ImportRunContext $context): array
    {
        $accessories = $row['accessories'] ?? '';
        if ('' === $accessories) {
            return [];
        }

        $messages = [];

        try {
            $ownerId = $this->resolveAssociationOwner($row, $context);
            if (null === $ownerId) {
                return [$this->associationError($rowIndex, $this->trans('The accessories owner could not be identified (no matching id or reference); the accessories were dropped.', 'Admin.Advparameters.Notification'))];
            }

            if (EntityImporterInterface::CLEAR_ASSOCIATION_MARKER === $accessories) {
                $this->commandBus->handle(new RemoveAllRelatedProductsCommand($ownerId));

                return [];
            }

            $accessoryIds = [];
            foreach ($this->valueParser->split($accessories, $context->getMultipleValueSeparator()) as $target) {
                $accessoryId = $this->resolveAccessoryTarget($target, $context);
                if (null === $accessoryId) {
                    // defensive re-check failure: error naming the association, link dropped, run completes
                    $messages[] = $this->associationError($rowIndex, $this->trans('Accessory "%target%" could not be resolved; the link was dropped.', 'Admin.Advparameters.Notification', ['%target%' => $target]));
                    continue;
                }
                $accessoryIds[] = $accessoryId;
            }

            if ([] !== $accessoryIds) {
                $this->commandBus->handle(new SetRelatedProductsCommand($ownerId, array_values(array_unique($accessoryIds))));
            }
        } catch (Throwable $e) {
            $messages[] = $this->associationError($rowIndex, $this->trans('Accessories could not be linked: %error%', 'Admin.Advparameters.Notification', ['%error%' => $e->getMessage()]));
        }

        return $messages;
    }

    /**
     * Re-derives the owning product from the row's identity columns (phases
     * share no state): reference first — independently of the match_ref
     * option, the row was just imported —, then the forced id.
     *
     * @param array<string, string> $row
     */
    private function resolveAssociationOwner(array $row, ImportRunContext $context): ?int
    {
        $reference = $row['reference'] ?? '';
        if ('' !== $reference) {
            $productId = $this->productLookup->getProductIdByReference($reference, $context->getShopId());
            if (null !== $productId) {
                return $productId;
            }
        }

        $id = $row['id'] ?? '';
        if ($context->getOptions()->forceIds && ctype_digit($id) && $this->productLookup->productExists((int) $id)) {
            return (int) $id;
        }

        return null;
    }

    private function resolveAccessoryTarget(string $target, ImportRunContext $context): ?int
    {
        if (ctype_digit($target)) {
            return $this->productLookup->productExists((int) $target) ? (int) $target : null;
        }

        return $this->productLookup->getProductIdByReference($target, $context->getShopId());
    }

    private function countDataRows(ImportRunContext $context): int
    {
        $physicalRows = 0;
        foreach ($this->fileReader->readFrom($context->getWorkingFile()) as $dataRow) {
            ++$physicalRows;
        }

        return max(0, $physicalRows - $context->getSkipRows());
    }

    /**
     * Whether this batch just consumed the last unit of the phase.
     */
    private function batchCompletesPhase(ImportRunContext $context, PhaseBatchResult $result): bool
    {
        return $context->getCurrentOffset() + $result->processedUnitCount >= $this->countDataRows($context);
    }

    /**
     * The pre-check runs on a context that already knows the rows this batch
     * skipped — clone + apply, since the caller only applies the result after
     * processPhaseBatch() returns.
     */
    private function contextWithBatchApplied(ImportRunContext $context, PhaseBatchResult $result): ImportRunContext
    {
        $clonedContext = clone $context;
        $clonedContext->applyBatchResult($result);

        return $clonedContext;
    }

    /**
     * @param list<ImportMessage> $messages
     */
    private function containsError(array $messages): bool
    {
        foreach ($messages as $message) {
            if (ImportMessage::SEVERITY_ERROR === $message->severity) {
                return true;
            }
        }

        return false;
    }

    private function associationError(int $rowIndex, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_ASSOCIATION, $rowIndex, 'accessories', $message);
    }

    private function assertKnownPhase(ImportPhaseDefinition $phase): void
    {
        $knownPhaseIds = array_map(static fn (ImportPhaseDefinition $definition): string => $definition->id, $this->getPhases());
        if (!in_array($phase->id, $knownPhaseIds, true)) {
            throw new UnknownPhaseException(sprintf('Unknown phase "%s" for the %s importer', $phase->id, self::ENTITY_TYPE));
        }
    }

    /**
     * @param array<string, string> $parameters
     */
    private function trans(string $id, string $domain, array $parameters = []): string
    {
        return $this->translator->trans($id, $parameters, $domain);
    }
}
