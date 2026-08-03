<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\DeleteProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImageDownloader;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\LanguageLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ProductImportWriterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ProductLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\TaxRulesGroupLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Database-phase import of one mapped product row: update-vs-create decision,
 * then one command dispatch per concern, in a fixed order. There is no
 * wrapping transaction (legacy parity): when a command fails mid-row the row
 * is reported as an error, the remaining commands are skipped and the row is
 * marked as skipped for the later phases.
 *
 * Localized values follow the single-language-file rule: on creation the
 * value is duplicated into every installed language, on update only the
 * file's language is written.
 */
final class ProductRowImporter
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ValueParser $valueParser,
        private readonly ProductIdentityResolver $identityResolver,
        private readonly ProductAssociationResolver $associationResolver,
        private readonly ProductLookup $productLookup,
        private readonly TaxRulesGroupLookup $taxRulesGroupLookup,
        private readonly LanguageLookup $languageLookup,
        private readonly ProductImportWriterInterface $importWriter,
        private readonly ImageDownloader $imageDownloader,
        private readonly ConfigurationInterface $configuration,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param array<string, string> $row mapped row values
     *
     * @return list<ImportMessage> an ERROR severity means the row failed and must be skipped by later phases
     */
    public function importRow(array $row, int $rowIndex, ImportRunContext $context): array
    {
        $messages = [];

        try {
            $languageId = $this->getLanguageId($context);
            $match = $this->identityResolver->resolve($row, $context);
            $isCreation = !$match->isUpdate();

            $productId = $this->resolveTargetProduct($row, $match, $context);

            $this->updateProductType($row, $match, $productId, $context);
            $manufacturerId = $this->resolveManufacturer($row, $context, $messages);
            $this->dispatchProductUpdate($row, $rowIndex, $productId, $isCreation, $languageId, $context, $manufacturerId, $messages);
            $this->dispatchStockUpdate($row, $productId, $context);
            $this->dispatchCategories($row, $rowIndex, $productId, $context, $messages);
            $this->dispatchSuppliers($row, $rowIndex, $productId, $messages);
            $this->dispatchTags($row, $productId, $isCreation, $languageId, $context);
            $this->dispatchFeatures($row, $rowIndex, $productId, $context, $messages);
            $this->dispatchImages($row, $rowIndex, $productId, $isCreation, $languageId, $context, $messages);
            $this->dispatchVirtualProductFile($row, $rowIndex, $productId, $messages);
            $this->dispatchCustomizationFields($row, $rowIndex, $productId, $isCreation, $languageId, $context, $messages);
            $this->dispatchSpecificPrice($row, $productId);
            $this->dispatchShops($row, $rowIndex, $productId, $context, $messages);
            $this->applyDateAdd($row, $productId);
        } catch (Throwable $e) {
            // deliberate catch-all: a failing command must fail THIS ROW only
            // (structured error, remaining commands skipped), never the batch
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_ERROR,
                ImportPhaseDefinition::PHASE_DATABASE,
                $rowIndex,
                null,
                $this->translator->trans('The row could not be fully imported: %error%', ['%error%' => $e->getMessage()], 'Admin.Advparameters.Notification')
            );
        }

        return $messages;
    }

    /**
     * @param array<string, string> $row
     */
    private function resolveTargetProduct(array $row, ProductMatch $match, ImportRunContext $context): int
    {
        if ($match->isUpdate()) {
            return (int) $match->productId;
        }

        $productType = $this->isVirtual($row) ? ProductType::TYPE_VIRTUAL : ProductType::TYPE_STANDARD;
        $localizedNames = $this->localizeForCreation($row['name'] ?? '');

        if (null !== $match->forcedId) {
            $this->importWriter->createProductWithId($match->forcedId, $productType, $context->getShopId(), $localizedNames);

            return $match->forcedId;
        }

        return $this->commandBus->handle(
            new AddProductCommand($productType, $context->getShopId(), $localizedNames)
        )->getValue();
    }

    /**
     * On update, the product type is only touched when the is_virtual column
     * is mapped, non-empty AND requests a virtual product — an explicit 0 is
     * left alone so that re-importing a non-virtual file cannot destroy an
     * existing virtual file association (fixes a legacy bug where
     * ProductDownload was deleted on every product row).
     *
     * @param array<string, string> $row
     */
    private function updateProductType(array $row, ProductMatch $match, int $productId, ImportRunContext $context): void
    {
        if (!$match->isUpdate()) {
            return;
        }

        if ('' !== ($row['is_virtual'] ?? '') && $this->isVirtual($row)) {
            $this->commandBus->handle(new UpdateProductTypeCommand($productId, ProductType::TYPE_VIRTUAL));
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function resolveManufacturer(array $row, ImportRunContext $context, array &$messages): ?int
    {
        $manufacturer = $row['manufacturer'] ?? '';
        if ('' === $manufacturer) {
            return null;
        }

        $resolved = $this->associationResolver->resolveManufacturer($manufacturer, $context);
        $messages = array_merge($messages, $resolved->messages);

        return $resolved->id;
    }

    /**
     * One UpdateProductCommand carrying every mapped scalar/localized field.
     *
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchProductUpdate(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context, ?int $manufacturerId, array &$messages): void
    {
        $command = new UpdateProductCommand($productId, $context->getShopConstraint());
        $hasUpdate = false;

        $localize = fn (string $value): array => $isCreation ? $this->localizeForCreation($value) : [$languageId => $value];

        // localized fields (name is only re-written on update: creation already set it)
        if (!$isCreation && '' !== ($row['name'] ?? '')) {
            $command->setLocalizedNames($localize($row['name']));
            $hasUpdate = true;
        }
        $localizedSetters = [
            'description' => 'setLocalizedDescriptions',
            'description_short' => 'setLocalizedShortDescriptions',
            'meta_title' => 'setLocalizedMetaTitles',
            'meta_description' => 'setLocalizedMetaDescriptions',
            'link_rewrite' => 'setLocalizedLinkRewrites',
            'available_now' => 'setLocalizedAvailableNowLabels',
            'available_later' => 'setLocalizedAvailableLaterLabels',
        ];
        foreach ($localizedSetters as $field => $setter) {
            if ('' !== ($row[$field] ?? '')) {
                $command->{$setter}($localize($row[$field]));
                $hasUpdate = true;
            }
        }

        if ('' !== ($row['delivery_in_stock'] ?? '') || '' !== ($row['delivery_out_stock'] ?? '')) {
            $command->setDeliveryTimeNoteType(DeliveryTimeNoteType::TYPE_SPECIFIC);
            if ('' !== ($row['delivery_in_stock'] ?? '')) {
                $command->setLocalizedDeliveryTimeInStockNotes($localize($row['delivery_in_stock']));
            }
            if ('' !== ($row['delivery_out_stock'] ?? '')) {
                $command->setLocalizedDeliveryTimeOutOfStockNotes($localize($row['delivery_out_stock']));
            }
            $hasUpdate = true;
        }

        // booleans
        $booleanSetters = [
            'active' => 'setActive',
            'on_sale' => 'setOnSale',
            'online_only' => 'setOnlineOnly',
            'available_for_order' => 'setAvailableForOrder',
            'show_price' => 'setShowPrice',
        ];
        foreach ($booleanSetters as $field => $setter) {
            if ('' !== ($row[$field] ?? '')) {
                $command->{$setter}($this->valueParser->parseBoolean($row[$field]) ?? false);
                $hasUpdate = true;
            }
        }

        // enums / plain strings
        if ('' !== ($row['visibility'] ?? '')) {
            $command->setVisibility($row['visibility']);
            $hasUpdate = true;
        }
        if ('' !== ($row['condition'] ?? '')) {
            $command->setCondition($row['condition']);
            $hasUpdate = true;
        }
        if ('' !== ($row['unity'] ?? '')) {
            $command->setUnity($row['unity']);
            $hasUpdate = true;
        }
        if ('' !== ($row['mpn'] ?? '')) {
            $command->setMpn($row['mpn']);
            $hasUpdate = true;
        }
        if ('' !== ($row['reference'] ?? '')) {
            $command->setReference($row['reference']);
            $hasUpdate = true;
        }
        if ('' !== ($row['ean13'] ?? '')) {
            $command->setGtin($row['ean13']);
            $hasUpdate = true;
        }
        if ('' !== ($row['isbn'] ?? '')) {
            $command->setIsbn($row['isbn']);
            $hasUpdate = true;
        }
        if ('' !== ($row['upc'] ?? '')) {
            $command->setUpc($row['upc']);
            $hasUpdate = true;
        }

        // prices
        $price = $this->resolvePrice($row);
        if (null !== $price) {
            $command->setPrice((string) $price);
            $hasUpdate = true;
        }
        $taxRulesGroupId = $row['id_tax_rules_group'] ?? '';
        if ('' !== $taxRulesGroupId && ctype_digit($taxRulesGroupId) && $this->taxRulesGroupLookup->taxRulesGroupExists((int) $taxRulesGroupId)) {
            $command->setTaxRulesGroupId((int) $taxRulesGroupId);
            $hasUpdate = true;
        }
        $decimalSetters = [
            'wholesale_price' => 'setWholesalePrice',
            'unit_price' => 'setUnitPrice',
            'additional_shipping_cost' => 'setAdditionalShippingCost',
            'width' => 'setWidth',
            'height' => 'setHeight',
            'depth' => 'setDepth',
            'weight' => 'setWeight',
        ];
        foreach ($decimalSetters as $field => $setter) {
            $value = $row[$field] ?? '';
            if ('' === $value) {
                continue;
            }
            $decimal = $this->valueParser->parseDecimal($value);
            if (null !== $decimal) {
                $command->{$setter}((string) $decimal);
                $hasUpdate = true;
            }
        }
        if ('' !== ($row['ecotax'] ?? '')) {
            $ecotax = (bool) $this->configuration->get('PS_USE_ECOTAX')
                ? $this->valueParser->parseDecimal($row['ecotax'])
                : new DecimalNumber('0');
            if (null !== $ecotax) {
                $command->setEcotax((string) $ecotax);
                $hasUpdate = true;
            }
        }

        // stock-related fields living on the product row
        if ('' !== ($row['minimal_quantity'] ?? '') && preg_match('/^[0-9]+$/', $row['minimal_quantity'])) {
            $command->setMinimalQuantity((int) $row['minimal_quantity']);
            $hasUpdate = true;
        }
        if ('' !== ($row['low_stock_threshold'] ?? '') && preg_match('/^-?[0-9]+$/', $row['low_stock_threshold'])) {
            $threshold = (int) $row['low_stock_threshold'];
            $command->setLowStockThreshold($threshold);
            $hasUpdate = true;

            // low_stock_alert has no standalone setter: the command derives
            // alert = (threshold != 0). Warn when the file disagrees.
            $alertValue = $row['low_stock_alert'] ?? '';
            if ('' !== $alertValue) {
                $requestedAlert = $this->valueParser->parseBoolean($alertValue) ?? false;
                if ($requestedAlert !== (0 !== $threshold)) {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_WARNING,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $rowIndex,
                        'low_stock_alert',
                        $this->translator->trans('The low stock alert follows the low stock level (enabled when the level is not 0); the low_stock_alert value was ignored.', [], 'Admin.Advparameters.Notification')
                    );
                }
            }
        }
        if ('' !== ($row['available_date'] ?? '')) {
            $availableDate = $this->valueParser->parseDate($row['available_date']);
            if (null !== $availableDate) {
                $command->setAvailableDate($availableDate);
                $hasUpdate = true;
            }
        }

        if (null !== $manufacturerId) {
            $command->setManufacturerId($manufacturerId);
            $hasUpdate = true;
        }

        if ($hasUpdate) {
            $this->commandBus->handle($command);
        }
    }

    /**
     * price_tex wins when both are present; price_tin is de-taxed with the
     * rate of the row's tax rules group (0 when absent/unknown) — legacy
     * parity.
     *
     * @param array<string, string> $row
     */
    private function resolvePrice(array $row): ?DecimalNumber
    {
        $priceTaxExcluded = $row['price_tex'] ?? '';
        if ('' !== $priceTaxExcluded) {
            return $this->valueParser->parseDecimal($priceTaxExcluded);
        }

        $priceTaxIncluded = $row['price_tin'] ?? '';
        if ('' === $priceTaxIncluded) {
            return null;
        }

        $price = $this->valueParser->parseDecimal($priceTaxIncluded);
        if (null === $price) {
            return null;
        }

        $taxRulesGroupId = $row['id_tax_rules_group'] ?? '';
        if ('' !== $taxRulesGroupId && ctype_digit($taxRulesGroupId) && $this->taxRulesGroupLookup->taxRulesGroupExists((int) $taxRulesGroupId)) {
            $rate = $this->taxRulesGroupLookup->getTaxRate((int) $taxRulesGroupId);
            $divisor = $rate->dividedBy(new DecimalNumber('100'), 6)->plus(new DecimalNumber('1'));
            $price = $price->dividedBy($divisor, 6);
        }

        return $price;
    }

    /**
     * @param array<string, string> $row
     */
    private function dispatchStockUpdate(array $row, int $productId, ImportRunContext $context): void
    {
        $command = new UpdateProductStockAvailableCommand($productId, $context->getShopConstraint());
        $hasUpdate = false;

        if ('' !== ($row['location'] ?? '')) {
            $command->setLocation($row['location']);
            $hasUpdate = true;
        }
        $outOfStock = $row['out_of_stock'] ?? '';
        if ('' !== $outOfStock && in_array((int) $outOfStock, [0, 1, 2], true)) {
            $command->setOutOfStockType((int) $outOfStock);
            $hasUpdate = true;
        }
        $quantity = $row['quantity'] ?? '';
        if ('' !== $quantity && preg_match('/^-?[0-9]+$/', $quantity)) {
            // the stock command only expresses deltas: read the current
            // quantity and convert the file's absolute value (delta 0 is
            // illegal and means nothing to change)
            $currentQuantity = $this->productLookup->getStockQuantity($productId, $context->getShopId()) ?? 0;
            $delta = (int) $quantity - $currentQuantity;
            if (0 !== $delta) {
                $command->setDeltaQuantity($delta);
                $hasUpdate = true;
            }
        }

        if ($hasUpdate) {
            $this->commandBus->handle($command);
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchCategories(array $row, int $rowIndex, int $productId, ImportRunContext $context, array &$messages): void
    {
        $categories = $row['category'] ?? '';
        if ('' === $categories) {
            return;
        }

        $entries = $this->valueParser->split($categories, $context->getMultipleValueSeparator());
        $resolved = $this->associationResolver->resolveCategories($entries, $context, $rowIndex);
        $messages = array_merge($messages, $resolved['messages']);

        if ([] === $resolved['ids']) {
            return;
        }

        $this->commandBus->handle(new SetAssociatedProductCategoriesCommand(
            $productId,
            $resolved['ids'][0], // legacy rule: the first entry is the default category
            $resolved['ids'],
            $context->getShopConstraint()
        ));
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchSuppliers(array $row, int $rowIndex, int $productId, array &$messages): void
    {
        $supplier = $row['supplier'] ?? '';
        if ('' === $supplier) {
            return;
        }

        $resolved = $this->associationResolver->resolveSupplier($supplier, $rowIndex);
        $messages = array_merge($messages, $resolved->messages);
        if (null === $resolved->id) {
            return;
        }

        $this->commandBus->handle(new SetSuppliersCommand($productId, [$resolved->id]));

        $supplierReference = $row['supplier_reference'] ?? '';
        $wholesalePrice = $this->valueParser->parseDecimal($row['wholesale_price'] ?? '') ?? new DecimalNumber('0');
        $this->commandBus->handle(new UpdateProductSuppliersCommand($productId, [
            [
                'supplier_id' => $resolved->id,
                'currency_id' => (int) $this->configuration->get('PS_CURRENCY_DEFAULT'),
                'reference' => $supplierReference,
                'price_tax_excluded' => (string) $wholesalePrice,
            ],
        ]));
        $this->commandBus->handle(new SetProductDefaultSupplierCommand($productId, $resolved->id));
    }

    /**
     * @param array<string, string> $row
     */
    private function dispatchTags(array $row, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): void
    {
        $tagsCell = $row['tags'] ?? '';
        if ('' === $tagsCell) {
            return;
        }

        $tags = $this->valueParser->split($tagsCell, $context->getMultipleValueSeparator());
        if ([] === $tags) {
            return;
        }

        $localizedTags = [];
        if ($isCreation) {
            foreach ($this->languageLookup->getAllLanguageIds() as $langId) {
                $localizedTags[$langId] = $tags;
            }
        } else {
            $localizedTags[$languageId] = $tags;
        }

        $this->commandBus->handle(new SetProductTagsCommand($productId, $localizedTags));
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchFeatures(array $row, int $rowIndex, int $productId, ImportRunContext $context, array &$messages): void
    {
        $featuresCell = $row['features'] ?? '';
        if ('' === $featuresCell) {
            return;
        }

        $entries = $this->valueParser->split($featuresCell, $context->getMultipleValueSeparator());
        $resolved = $this->associationResolver->resolveFeatures($entries, $context, $rowIndex);
        $messages = array_merge($messages, $resolved['messages']);

        if ([] !== $resolved['featureValues']) {
            $this->commandBus->handle(new SetProductFeatureValuesCommand($productId, $resolved['featureValues']));
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchImages(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context, array &$messages): void
    {
        if ('' !== ($row['delete_existing_images'] ?? '') && true === $this->valueParser->parseBoolean($row['delete_existing_images'])) {
            /** @var array<int, \PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage> $existingImages */
            $existingImages = $this->commandBus->handle(new GetProductImages($productId, $context->getShopConstraint()));
            foreach ($existingImages as $existingImage) {
                $this->commandBus->handle(new DeleteProductImageCommand($existingImage->getImageId()));
            }
        }

        $imagesCell = $row['image'] ?? '';
        if ('' === $imagesCell) {
            return;
        }

        $imageUrls = $this->valueParser->split($imagesCell, $context->getMultipleValueSeparator());
        $imageAlts = $this->valueParser->split($row['image_alt'] ?? '', $context->getMultipleValueSeparator());

        foreach ($imageUrls as $index => $imageUrl) {
            try {
                $temporaryFile = $this->imageDownloader->download($imageUrl);
            } catch (FileDownloadException $e) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $rowIndex,
                    'image',
                    $this->translator->trans('Image "%url%" could not be fetched and was skipped: %error%', ['%url%' => $imageUrl, '%error%' => $e->getMessage()], 'Admin.Advparameters.Notification')
                );
                continue;
            }

            try {
                $imageId = $this->commandBus->handle(
                    new AddProductImageCommand($productId, $temporaryFile, $context->getShopConstraint())
                )->getValue();

                $alt = $imageAlts[$index] ?? '';
                if ('' !== $alt) {
                    $legendCommand = new UpdateProductImageCommand($imageId, $context->getShopConstraint());
                    $legendCommand->setLocalizedLegends($isCreation ? $this->localizeForCreation($alt) : [$languageId => $alt]);
                    $this->commandBus->handle($legendCommand);
                }
            } finally {
                @unlink($temporaryFile);
            }
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchVirtualProductFile(array $row, int $rowIndex, int $productId, array &$messages): void
    {
        $fileUrl = $row['file_url'] ?? '';
        if ('' === $fileUrl || !$this->isVirtual($row)) {
            return;
        }

        try {
            $temporaryFile = $this->imageDownloader->download($fileUrl);
        } catch (FileDownloadException $e) {
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $rowIndex,
                'file_url',
                $this->translator->trans('Virtual product file "%url%" could not be fetched and was skipped: %error%', ['%url%' => $fileUrl, '%error%' => $e->getMessage()], 'Admin.Advparameters.Notification')
            );

            return;
        }

        try {
            $accessDays = preg_match('/^[0-9]+$/', $row['nb_days_accessible'] ?? '') ? (int) $row['nb_days_accessible'] : null;
            $downloadTimesLimit = preg_match('/^[0-9]+$/', $row['nb_downloadable'] ?? '') ? (int) $row['nb_downloadable'] : null;
            $expirationDate = $this->valueParser->parseDate($row['date_expiration'] ?? '');

            $this->commandBus->handle(new AddVirtualProductFileCommand(
                $productId,
                $temporaryFile,
                basename(parse_url($fileUrl, PHP_URL_PATH) ?: $fileUrl) ?: 'file',
                $accessDays,
                $downloadTimesLimit,
                $expirationDate
            ));
        } finally {
            @unlink($temporaryFile);
        }
    }

    /**
     * Legacy set bare customizability counters on the product row;
     * the reviewed replacement creates one real generic customization field
     * per requested kind (file upload / text).
     *
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchCustomizationFields(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context, array &$messages): void
    {
        $wantsFileField = true === $this->valueParser->parseBoolean($row['uploadable_files'] ?? '');
        $wantsTextField = true === $this->valueParser->parseBoolean($row['text_fields'] ?? '');
        $customizable = true === $this->valueParser->parseBoolean($row['customizable'] ?? '');

        if (!$wantsFileField && !$wantsTextField) {
            if ($customizable) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $rowIndex,
                    'customizable',
                    $this->translator->trans('"customizable" requires at least one of uploadable_files/text_fields; no customization field was created.', [], 'Admin.Advparameters.Notification')
                );
            }

            return;
        }

        $label = $this->translator->trans('Customization', [], 'Admin.Global');
        $localizedNames = $isCreation ? $this->localizeForCreation($label) : [$languageId => $label];

        $fields = [];
        if ($wantsFileField) {
            $fields[] = [
                'type' => CustomizationFieldType::TYPE_FILE,
                'localized_names' => $localizedNames,
                'is_required' => false,
                'added_by_module' => false,
            ];
        }
        if ($wantsTextField) {
            $fields[] = [
                'type' => CustomizationFieldType::TYPE_TEXT,
                'localized_names' => $localizedNames,
                'is_required' => false,
                'added_by_module' => false,
            ];
        }

        $this->commandBus->handle(new SetProductCustomizationFieldsCommand($productId, $fields, $context->getShopConstraint()));
    }

    /**
     * Legacy "basic reduction": one specific price rule, all currencies/
     * countries/groups, from quantity 1.
     *
     * @param array<string, string> $row
     */
    private function dispatchSpecificPrice(array $row, int $productId): void
    {
        $reductionPrice = $this->valueParser->parseDecimal($row['reduction_price'] ?? '');
        $reductionPercent = $this->valueParser->parseDecimal($row['reduction_percent'] ?? '');

        if (null === $reductionPrice && null === $reductionPercent) {
            return;
        }

        $reductionType = null !== $reductionPrice ? Reduction::TYPE_AMOUNT : Reduction::TYPE_PERCENTAGE;
        $reductionValue = null !== $reductionPrice ? $reductionPrice : $reductionPercent;

        $from = $this->valueParser->parseDate($row['reduction_from'] ?? '');
        $to = $this->valueParser->parseDate($row['reduction_to'] ?? '');

        $this->commandBus->handle(new AddSpecificPriceCommand(
            $productId,
            $reductionType,
            (string) $reductionValue,
            true,
            '-1',
            1,
            null !== $from ? DateTime::createFromImmutable($from) : new NullDateTime(),
            null !== $to ? DateTime::createFromImmutable($to) : new NullDateTime()
        ));
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function dispatchShops(array $row, int $rowIndex, int $productId, ImportRunContext $context, array &$messages): void
    {
        $shopCell = $row['shop'] ?? '';
        if ('' === $shopCell) {
            return;
        }

        $resolved = $this->associationResolver->resolveShops($shopCell, $context, $rowIndex);
        $messages = array_merge($messages, $resolved['messages']);

        $shopIds = $resolved['shopIds'];
        // the source shop must be part of the association (command constraint);
        // the run's shop holds the data that was just written
        if (!in_array($context->getShopId(), $shopIds, true)) {
            $shopIds[] = $context->getShopId();
        }

        if ([$context->getShopId()] === $shopIds) {
            return;
        }

        $this->commandBus->handle(new SetProductShopsCommand($productId, $context->getShopId(), $shopIds));
    }

    /**
     * @param array<string, string> $row
     */
    private function applyDateAdd(array $row, int $productId): void
    {
        $dateAdd = $this->valueParser->parseDate($row['date_add'] ?? '');
        if (null !== $dateAdd) {
            $this->importWriter->setDateAdd($productId, $dateAdd);
        }
    }

    /**
     * @param array<string, string> $row
     */
    private function isVirtual(array $row): bool
    {
        return true === $this->valueParser->parseBoolean($row['is_virtual'] ?? '');
    }

    /**
     * @return array<int, string>
     */
    private function localizeForCreation(string $value): array
    {
        $localized = [];
        foreach ($this->languageLookup->getAllLanguageIds() as $languageId) {
            $localized[$languageId] = $value;
        }

        return $localized;
    }

    private function getLanguageId(ImportRunContext $context): int
    {
        return $this->languageLookup->getLanguageIdByIsoCode($context->getLangIso())
            ?? throw new ImportEngineException(sprintf('Unknown language iso code "%s"', $context->getLangIso()));
    }
}
