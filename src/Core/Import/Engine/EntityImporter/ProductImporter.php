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
        return $this->trans('Products', 'Admin.Global');
    }

    public function getFields(): EntityFieldCollectionInterface
    {
        // built once per instance: the collection is immutable and getFields()
        // is called by every RowMapper::map()
        if (null !== $this->fields) {
            return $this->fields;
        }

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
            new EntityField('gtin', $this->trans('GTIN', 'Admin.Catalog.Feature')),
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
            new EntityField('uploadable_files', $this->trans('Number of file upload fields to create', 'Admin.Advparameters.Feature')),
            new EntityField('text_fields', $this->trans('Number of text fields to create', 'Admin.Advparameters.Feature')),
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

        return $this->fields = EntityFieldCollection::createFromArray($fields);
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
                self::PHASE_ASSOCIATION_VALIDATION,
                $this->trans('Checking accessories', 'Admin.Advparameters.Feature'),
                true
            ),
            new ImportPhaseDefinition(
                ImportPhaseDefinition::PHASE_ASSOCIATION,
                $this->trans('Linking accessories', 'Admin.Advparameters.Feature')
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
                    $this->trans('Importing accessories requires an id column (with the force IDs option) or a reference column to identify the owning product; the accessories will be dropped.', 'Admin.Advparameters.Notification'),
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
                        $this->trans('The row is empty and was skipped.', 'Admin.Advparameters.Notification'),
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

        if (null === $this->resolveAssociationOwner($row, $context)) {
            $messages[] = $this->accessoryWarning($rowIndex, $this->trans('The accessories owner could not be identified (no matching id or reference); the accessories will be dropped.', 'Admin.Advparameters.Notification'), self::PHASE_ASSOCIATION_VALIDATION);
        }

        foreach ($this->valueParser->split($accessories, $context->getMultipleValueSeparator()) as $target) {
            $resolved = $this->identityResolver->resolveProductTarget($target, $context);

            if ($resolved['ambiguous']) {
                $messages[] = $this->accessoryWarning($rowIndex, $this->trans('Accessory "%target%" matches both a product id and a product reference; it will be linked by id.', 'Admin.Advparameters.Notification', ['%target%' => $target]), self::PHASE_ASSOCIATION_VALIDATION);
            } elseif (null === $resolved['resolvedId']) {
                $messages[] = $this->accessoryWarning($rowIndex, $this->trans('Accessory "%target%" matches no product; the link will be dropped.', 'Admin.Advparameters.Notification', ['%target%' => $target]), self::PHASE_ASSOCIATION_VALIDATION);
            } elseif (ProductIdentityResolver::TARGET_MATCHED_BY_REFERENCE === $resolved['matchedBy'] && ctype_digit($target)) {
                $messages[] = $this->accessoryWarning($rowIndex, $this->trans('Accessory "%target%" matches no product id; it will be linked by reference.', 'Admin.Advparameters.Notification', ['%target%' => $target]), self::PHASE_ASSOCIATION_VALIDATION);
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
            $ownerId = $this->resolveAssociationOwner($row, $context);
            if (null === $ownerId) {
                return [$this->accessoryError($rowIndex, $this->trans('The accessories owner could not be identified (no matching id or reference); the accessories were dropped.', 'Admin.Advparameters.Notification'))];
            }

            if (EntityImporterInterface::CLEAR_ASSOCIATION_MARKER === $accessories) {
                $this->commandBus->handle(new RemoveAllRelatedProductsCommand($ownerId));

                return [];
            }

            $accessoryIds = [];
            foreach ($this->valueParser->split($accessories, $context->getMultipleValueSeparator()) as $target) {
                $resolved = $this->identityResolver->resolveProductTarget($target, $context);

                if ($resolved['ambiguous']) {
                    $messages[] = $this->accessoryWarning($rowIndex, $this->trans('Accessory "%target%" matches both a product id and a product reference; it was linked by id.', 'Admin.Advparameters.Notification', ['%target%' => $target]));
                } elseif (null === $resolved['resolvedId']) {
                    $messages[] = $this->accessoryError($rowIndex, $this->trans('Accessory "%target%" could not be resolved; the link was dropped.', 'Admin.Advparameters.Notification', ['%target%' => $target]));
                } elseif (ProductIdentityResolver::TARGET_MATCHED_BY_REFERENCE === $resolved['matchedBy'] && ctype_digit($target)) {
                    $messages[] = $this->accessoryWarning($rowIndex, $this->trans('Accessory "%target%" matches no product id; it was linked by reference.', 'Admin.Advparameters.Notification', ['%target%' => $target]));
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
            $messages[] = $this->accessoryError($rowIndex, $this->trans('Accessories could not be linked: %error%', 'Admin.Advparameters.Notification', ['%error%' => $e->getMessage()]));
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
    protected function resolveAssociationOwner(array $row, ImportRunContext $context): ?int
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

    /**
     * @param array<string, string> $parameters
     */
    protected function trans(string $id, string $domain, array $parameters = []): string
    {
        return $this->translator->trans($id, $parameters, $domain);
    }
}
