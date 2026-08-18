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
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductIdentityResolver;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowValidator;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;
use PrestaShop\PrestaShop\Core\Import\File\ResumableFileReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Product importer: validation (pausing, no writes) -> database (one CQRS
 * command dispatch chain per row) -> association_validation (pausing, no
 * writes: every row exists in the database by then, so accessories targets
 * are checked with per-batch DB probes — stateless and cursor-resumable) ->
 * association (accessories links, resolved after every row exists so file
 * order never matters, including mutual A<->B references).
 */
class ProductImporter extends AbstractEntityImporter
{
    public const ENTITY_TYPE = 'product';

    /**
     * Product-specific phase (the ids are an open string model): pausing
     * pre-check of the accessories targets, between database and association.
     */
    public const PHASE_ASSOCIATION_VALIDATION = 'association_validation';

    protected ?EntityFieldCollectionInterface $fields = null;

    public function __construct(
        protected readonly TranslatorInterface $translator,
        ResumableFileReaderInterface $fileReader,
        RowMapper $rowMapper,
        protected readonly ProductRowValidator $rowValidator,
        protected readonly ProductRowImporter $rowImporter,
        protected readonly ProductIdentityResolver $identityResolver,
        protected readonly ValueParser $valueParser,
        protected readonly CommandBusInterface $commandBus,
        protected readonly LoggerInterface $logger,
    ) {
        parent::__construct($fileReader, $rowMapper);
    }

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function getLabel(): string
    {
        return $this->translator->trans('Products', [], 'Admin.Global');
    }

    public function getFields(): EntityFieldCollectionInterface
    {
        // built once per instance: the collection is immutable and getFields()
        // is called by every RowMapper::map()
        if (null !== $this->fields) {
            return $this->fields;
        }

        $fields = [
            new EntityField('id', $this->translator->trans('ID', [], 'Admin.Global')),
            new EntityField('active', $this->translator->trans('Active (0/1)', [], 'Admin.Advparameters.Feature')),
            new EntityField('name', $this->translator->trans('Name', [], 'Admin.Global'), '', true),
            new EntityField('category', $this->translator->trans('Categories (x,y,z...)', [], 'Admin.Advparameters.Feature')),
            new EntityField('price_tex', $this->translator->trans('Price tax excluded', [], 'Admin.Advparameters.Feature')),
            new EntityField('price_tin', $this->translator->trans('Price tax included', [], 'Admin.Advparameters.Feature')),
            new EntityField('id_tax_rules_group', $this->translator->trans('Tax rule ID', [], 'Admin.Advparameters.Feature')),
            new EntityField('wholesale_price', $this->translator->trans('Cost price', [], 'Admin.Catalog.Feature')),
            new EntityField('on_sale', $this->translator->trans('On sale (0/1)', [], 'Admin.Advparameters.Feature')),
            new EntityField('reduction_price', $this->translator->trans('Discount amount', [], 'Admin.Advparameters.Feature')),
            new EntityField('reduction_percent', $this->translator->trans('Discount percent', [], 'Admin.Advparameters.Feature')),
            new EntityField('reduction_from', $this->translator->trans('Discount from (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')),
            new EntityField('reduction_to', $this->translator->trans('Discount to (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')),
            new EntityField('reference', $this->translator->trans('Reference #', [], 'Admin.Advparameters.Feature')),
            new EntityField('supplier_reference', $this->translator->trans('Supplier reference #', [], 'Admin.Advparameters.Feature')),
            new EntityField('supplier', $this->translator->trans('Supplier', [], 'Admin.Global')),
            new EntityField('manufacturer', $this->translator->trans('Brand', [], 'Admin.Global')),
            new EntityField('gtin', $this->translator->trans('GTIN', [], 'Admin.Catalog.Feature')),
            new EntityField('ean13', $this->translator->trans('EAN-13', [], 'Admin.Advparameters.Feature')),
            new EntityField('isbn', $this->translator->trans('ISBN', [], 'Admin.Catalog.Feature')),
            new EntityField('upc', $this->translator->trans('UPC', [], 'Admin.Advparameters.Feature')),
            new EntityField('mpn', $this->translator->trans('MPN', [], 'Admin.Catalog.Feature')),
            new EntityField('ecotax', $this->translator->trans('Ecotax', [], 'Admin.Catalog.Feature')),
            new EntityField('width', $this->translator->trans('Width', [], 'Admin.Global')),
            new EntityField('height', $this->translator->trans('Height', [], 'Admin.Global')),
            new EntityField('depth', $this->translator->trans('Depth', [], 'Admin.Global')),
            new EntityField('weight', $this->translator->trans('Weight', [], 'Admin.Global')),
            new EntityField('delivery_in_stock', $this->translator->trans('Delivery time of in-stock products:', [], 'Admin.Catalog.Feature')),
            new EntityField('delivery_out_stock', $this->translator->trans('Delivery time of out-of-stock products with allowed orders:', [], 'Admin.Advparameters.Feature')),
            new EntityField('quantity', $this->translator->trans('Quantity', [], 'Admin.Global')),
            new EntityField('minimal_quantity', $this->translator->trans('Minimal quantity', [], 'Admin.Advparameters.Feature')),
            new EntityField('low_stock_threshold', $this->translator->trans('Low stock level', [], 'Admin.Catalog.Feature')),
            new EntityField('low_stock_alert', $this->translator->trans('Receive a low stock alert by email', [], 'Admin.Catalog.Feature')),
            new EntityField('location', $this->translator->trans('Stock location', [], 'Admin.Catalog.Feature')),
            new EntityField('visibility', $this->translator->trans('Visibility', [], 'Admin.Catalog.Feature')),
            new EntityField('additional_shipping_cost', $this->translator->trans('Additional shipping cost', [], 'Admin.Advparameters.Feature')),
            new EntityField('unity', $this->translator->trans('Unit for the price per unit', [], 'Admin.Advparameters.Feature')),
            new EntityField('unit_price', $this->translator->trans('Price per unit', [], 'Admin.Advparameters.Feature')),
            new EntityField('description_short', $this->translator->trans('Summary', [], 'Admin.Catalog.Feature')),
            new EntityField('description', $this->translator->trans('Description', [], 'Admin.Global')),
            new EntityField('tags', $this->translator->trans('Tags (x,y,z...)', [], 'Admin.Advparameters.Feature')),
            new EntityField('meta_title', $this->translator->trans('Meta title', [], 'Admin.Global')),
            new EntityField('meta_description', $this->translator->trans('Meta description', [], 'Admin.Global')),
            new EntityField('link_rewrite', $this->translator->trans('Rewritten URL', [], 'Admin.Advparameters.Feature')),
            new EntityField('available_now', $this->translator->trans('Label when in stock', [], 'Admin.Catalog.Feature')),
            new EntityField('available_later', $this->translator->trans('Label when backorder allowed', [], 'Admin.Advparameters.Feature')),
            new EntityField('available_for_order', $this->translator->trans('Available for order (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')),
            new EntityField('available_date', $this->translator->trans('Product availability date', [], 'Admin.Advparameters.Feature')),
            new EntityField('date_add', $this->translator->trans('Product creation date', [], 'Admin.Advparameters.Feature')),
            new EntityField('show_price', $this->translator->trans('Show price (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')),
            new EntityField('image', $this->translator->trans('Image URLs (x,y,z...)', [], 'Admin.Advparameters.Feature')),
            new EntityField('image_alt', $this->translator->trans('Image alt texts (x,y,z...)', [], 'Admin.Advparameters.Feature')),
            new EntityField('delete_existing_images', $this->translator->trans('Delete existing images (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')),
            new EntityField('features', $this->translator->trans('Feature (Name:Value:Position:Customized)', [], 'Admin.Advparameters.Feature')),
            new EntityField('online_only', $this->translator->trans('Available online only (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')),
            new EntityField('condition', $this->translator->trans('Condition', [], 'Admin.Catalog.Feature')),
            new EntityField('customizable', $this->translator->trans('Customizable (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')),
            new EntityField('uploadable_files', $this->translator->trans('Number of file upload fields to create', [], 'Admin.Advparameters.Feature')),
            new EntityField('text_fields', $this->translator->trans('Number of text fields to create', [], 'Admin.Advparameters.Feature')),
            new EntityField('out_of_stock', $this->translator->trans('Action when out of stock', [], 'Admin.Advparameters.Feature')),
            new EntityField('is_virtual', $this->translator->trans('Virtual product (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')),
            new EntityField('file_url', $this->translator->trans('File URL', [], 'Admin.Advparameters.Feature')),
            new EntityField(
                'nb_downloadable',
                $this->translator->trans('Number of allowed downloads', [], 'Admin.Catalog.Feature'),
                $this->translator->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', [], 'Admin.Catalog.Help')
            ),
            new EntityField('date_expiration', $this->translator->trans('Expiration date (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')),
            new EntityField(
                'nb_days_accessible',
                $this->translator->trans('Number of days', [], 'Admin.Advparameters.Feature'),
                $this->translator->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', [], 'Admin.Catalog.Help')
            ),
            new EntityField(
                'shop',
                $this->translator->trans('ID / Name of the store', [], 'Admin.Advparameters.Feature'),
                $this->translator->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', [], 'Admin.Advparameters.Help')
            ),
            new EntityField('accessories', $this->translator->trans('Accessories (x,y,z...)', [], 'Admin.Advparameters.Feature')),
        ];

        return $this->fields = EntityFieldCollection::createFromArray($fields);
    }

    public function getPhases(): array
    {
        return [
            new ImportPhaseDefinition(
                ImportPhaseDefinition::PHASE_VALIDATION,
                $this->translator->trans('Validating the file', [], 'Admin.Advparameters.Feature'),
                true
            ),
            new ImportPhaseDefinition(
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Importing products', [], 'Admin.Advparameters.Feature')
            ),
            new ImportPhaseDefinition(
                self::PHASE_ASSOCIATION_VALIDATION,
                $this->translator->trans('Checking accessories', [], 'Admin.Advparameters.Feature'),
                true
            ),
            new ImportPhaseDefinition(
                ImportPhaseDefinition::PHASE_ASSOCIATION,
                $this->translator->trans('Linking accessories', [], 'Admin.Advparameters.Feature')
            ),
        ];
    }

    public function countPhaseUnits(string $phaseId, ImportRunContext $context): int
    {
        $this->assertKnownPhase($phaseId);

        $accessoriesPhases = [self::PHASE_ASSOCIATION_VALIDATION, ImportPhaseDefinition::PHASE_ASSOCIATION];
        if (in_array($phaseId, $accessoriesPhases, true) && !$context->isFieldMapped('accessories')) {
            return 0;
        }

        return parent::countPhaseUnits($phaseId, $context);
    }

    public function processPhaseBatch(string $phaseId, ImportRunContext $context, int $limit): PhaseBatchResult
    {
        $this->assertKnownPhase($phaseId);

        return match ($phaseId) {
            ImportPhaseDefinition::PHASE_VALIDATION => $this->processValidationBatch($context, $limit),
            ImportPhaseDefinition::PHASE_DATABASE => $this->processDatabaseBatch($context, $limit),
            self::PHASE_ASSOCIATION_VALIDATION => $this->processAssociationValidationBatch($context, $limit),
            default => $this->processAssociationBatch($context, $limit),
        };
    }

    protected function processValidationBatch(ImportRunContext $context, int $limit): PhaseBatchResult
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
                    $this->translator->trans('Importing accessories requires an id column (with the force IDs option) or a reference column to identify the owning product; the accessories will be dropped.', [], 'Admin.Advparameters.Notification'),
                    null,
                    'accessories'
                );
            }
        }

        $result = $this->iterateBatch($context, $limit, function (array $row, int $rowIndex) use ($context): array {
            if (self::isEmptyMappedRow($row)) {
                return [
                    'messages' => [new ImportMessage(
                        ImportMessage::SEVERITY_NOTICE,
                        ImportPhaseDefinition::PHASE_VALIDATION,
                        $this->translator->trans('The row is empty and was skipped.', [], 'Admin.Advparameters.Notification'),
                        $rowIndex
                    )],
                    'skipped' => true,
                ];
            }

            $rowMessages = $this->rowValidator->validate($row, $rowIndex, $context);

            return ['messages' => $rowMessages, 'skipped' => $this->containsError($rowMessages)];
        });

        return new PhaseBatchResult($result->processedUnitCount, array_merge($messages, $result->messages), $result->newlySkippedRows, $result->resumeCursor);
    }

    protected function processDatabaseBatch(ImportRunContext $context, int $limit): PhaseBatchResult
    {
        return $this->iterateBatch($context, $limit, function (array $row, int $rowIndex) use ($context): array {
            if ($context->isRowSkipped($rowIndex)) {
                return ['messages' => [], 'skipped' => false];
            }

            $rowMessages = $this->rowImporter->importRow($row, $rowIndex, $context);

            return ['messages' => $rowMessages, 'skipped' => $this->containsError($rowMessages)];
        });
    }

    /**
     * Pausing pre-check of the accessories targets, once every row exists in
     * the database: pure per-batch DB probes through the same decision point
     * as the association phase (no cross-batch state, cursor-resumable).
     * Misses are warnings — the association phase will drop the links.
     */
    protected function processAssociationValidationBatch(ImportRunContext $context, int $limit): PhaseBatchResult
    {
        return $this->iterateBatch($context, $limit, function (array $row, int $rowIndex) use ($context): array {
            if ($context->isRowSkipped($rowIndex)) {
                return ['messages' => [], 'skipped' => false];
            }

            return ['messages' => $this->checkAccessories($row, $rowIndex, $context), 'skipped' => false];
        });
    }

    protected function processAssociationBatch(ImportRunContext $context, int $limit): PhaseBatchResult
    {
        return $this->iterateBatch($context, $limit, function (array $row, int $rowIndex) use ($context): array {
            if ($context->isRowSkipped($rowIndex)) {
                return ['messages' => [], 'skipped' => false];
            }

            return ['messages' => $this->associateAccessories($row, $rowIndex, $context), 'skipped' => false];
        });
    }

    /**
     * @param array<string, string> $row
     *
     * @return list<ImportMessage>
     */
    protected function checkAccessories(array $row, int $rowIndex, ImportRunContext $context): array
    {
        $accessories = $row['accessories'] ?? '';
        if ('' === $accessories || EntityImporterInterface::CLEAR_ASSOCIATION_MARKER === $accessories) {
            return [];
        }

        $messages = [];

        $owner = $this->resolveAssociationOwner($row, $context);
        if (!$owner->isUpdate()) {
            $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('The accessories owner could not be identified (no matching id or reference); the accessories will be dropped.', [], 'Admin.Advparameters.Notification'), self::PHASE_ASSOCIATION_VALIDATION);
        } elseif ($owner->matchCount > 1) {
            $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('The reference "%reference%" matches %count% products; the accessories will be attached to the first one.', ['%reference%' => $row['reference'] ?? '', '%count%' => $owner->matchCount], 'Admin.Advparameters.Notification'), self::PHASE_ASSOCIATION_VALIDATION);
        }

        foreach ($this->valueParser->split($accessories, $context->getMultipleValueSeparator()) as $target) {
            $resolved = $this->identityResolver->resolveProductTarget($target, $context);

            if ($resolved['ambiguous']) {
                $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('Accessory "%target%" matches both a product id and a product reference; it will be linked by id.', ['%target%' => $target], 'Admin.Advparameters.Notification'), self::PHASE_ASSOCIATION_VALIDATION);
            } elseif (null === $resolved['resolvedId']) {
                $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('Accessory "%target%" matches no product; the link will be dropped.', ['%target%' => $target], 'Admin.Advparameters.Notification'), self::PHASE_ASSOCIATION_VALIDATION);
            } else {
                if ($resolved['referenceMatchCount'] > 1) {
                    $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('Accessory "%target%" matches %count% products; it will be linked to the first one.', ['%target%' => $target, '%count%' => $resolved['referenceMatchCount']], 'Admin.Advparameters.Notification'), self::PHASE_ASSOCIATION_VALIDATION);
                }
                if (ProductIdentityResolver::TARGET_MATCHED_BY_REFERENCE === $resolved['matchedBy'] && ctype_digit($target)) {
                    $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('Accessory "%target%" matches no product id; it will be linked by reference.', ['%target%' => $target], 'Admin.Advparameters.Notification'), self::PHASE_ASSOCIATION_VALIDATION);
                }
            }
        }

        return $messages;
    }

    /**
     * @param array<string, string> $row
     *
     * @return list<ImportMessage>
     */
    protected function associateAccessories(array $row, int $rowIndex, ImportRunContext $context): array
    {
        $accessories = $row['accessories'] ?? '';
        if ('' === $accessories) {
            return [];
        }

        $messages = [];

        try {
            $owner = $this->resolveAssociationOwner($row, $context);
            if (!$owner->isUpdate()) {
                return [$this->accessoryError($rowIndex, $this->translator->trans('The accessories owner could not be identified (no matching id or reference); the accessories were dropped.', [], 'Admin.Advparameters.Notification'))];
            }
            $ownerId = (int) $owner->entityId;
            if ($owner->matchCount > 1) {
                $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('The reference "%reference%" matches %count% products; the accessories were attached to the first one.', ['%reference%' => $row['reference'] ?? '', '%count%' => $owner->matchCount], 'Admin.Advparameters.Notification'));
            }

            if (EntityImporterInterface::CLEAR_ASSOCIATION_MARKER === $accessories) {
                $this->commandBus->handle(new RemoveAllRelatedProductsCommand($ownerId));

                return [];
            }

            $accessoryIds = [];
            foreach ($this->valueParser->split($accessories, $context->getMultipleValueSeparator()) as $target) {
                $resolved = $this->identityResolver->resolveProductTarget($target, $context);

                if ($resolved['ambiguous']) {
                    $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('Accessory "%target%" matches both a product id and a product reference; it was linked by id.', ['%target%' => $target], 'Admin.Advparameters.Notification'));
                } elseif (null === $resolved['resolvedId']) {
                    $messages[] = $this->accessoryError($rowIndex, $this->translator->trans('Accessory "%target%" could not be resolved; the link was dropped.', ['%target%' => $target], 'Admin.Advparameters.Notification'));
                } else {
                    if ($resolved['referenceMatchCount'] > 1) {
                        $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('Accessory "%target%" matches %count% products; it was linked to the first one.', ['%target%' => $target, '%count%' => $resolved['referenceMatchCount']], 'Admin.Advparameters.Notification'));
                    }
                    if (ProductIdentityResolver::TARGET_MATCHED_BY_REFERENCE === $resolved['matchedBy'] && ctype_digit($target)) {
                        $messages[] = $this->accessoryWarning($rowIndex, $this->translator->trans('Accessory "%target%" matches no product id; it was linked by reference.', ['%target%' => $target], 'Admin.Advparameters.Notification'));
                    }
                }

                if (null !== $resolved['resolvedId']) {
                    $accessoryIds[] = $resolved['resolvedId'];
                }
            }

            if ([] !== $accessoryIds) {
                $this->commandBus->handle(new SetRelatedProductsCommand($ownerId, array_values(array_unique($accessoryIds))));
            }
        } catch (Throwable $e) {
            $this->logger->error('Import: accessories could not be linked', ['row' => $rowIndex, 'exception' => $e]);
            $messages[] = $this->accessoryError($rowIndex, $this->translator->trans('Accessories could not be linked: %error%', ['%error%' => $e->getMessage()], 'Admin.Advparameters.Notification'));
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
    protected function resolveAssociationOwner(array $row, ImportRunContext $context): EntityMatch
    {
        $id = $row['id'] ?? '';
        $usableId = $context->getOptions()->forceIds && ctype_digit($id) ? (int) $id : null;

        return $this->identityResolver->findExistingByReferenceThenId($row['reference'] ?? '', $usableId, $context);
    }

    protected function accessoryError(int $rowIndex, string $message, string $phaseId = ImportPhaseDefinition::PHASE_ASSOCIATION): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_ERROR, $phaseId, $message, $rowIndex, 'accessories');
    }

    protected function accessoryWarning(int $rowIndex, string $message, string $phaseId = ImportPhaseDefinition::PHASE_ASSOCIATION): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_WARNING, $phaseId, $message, $rowIndex, 'accessories');
    }
}
