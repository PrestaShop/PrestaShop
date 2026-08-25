<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use DateTime;
use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\NoCountryId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NoCurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\NoGroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\NoCustomerId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\RemoveAllCustomizationFieldsFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\DeleteProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\EditSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\FoundEntity;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\ProductFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\ShopFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\SupplierFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver\CategoryResolver;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver\FeatureResolver;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver\ManufacturerResolver;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\FileDownloader;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
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
class ProductRowImporter
{
    use LocalizedValueTrait;
    use ProductIdentityMessagesTrait;

    /**
     * @var array<int, DecimalNumber> memoized (1 + rate/100) per tax rules group
     */
    protected array $taxDivisors = [];

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly ValueParser $valueParser,
        protected readonly ProductFinder $productFinder,
        protected readonly SupplierFinder $supplierFinder,
        protected readonly ShopFinder $shopFinder,
        protected readonly ManufacturerResolver $manufacturerResolver,
        protected readonly CategoryResolver $categoryResolver,
        protected readonly FeatureResolver $featureResolver,
        protected readonly ProductRepository $productRepository,
        protected readonly ProductSupplierRepository $productSupplierRepository,
        protected readonly SpecificPriceRepository $specificPriceRepository,
        protected readonly StockAvailableRepository $stockAvailableRepository,
        protected readonly VirtualProductFileRepository $virtualProductFileRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
        protected readonly TaxComputer $taxComputer,
        protected readonly LanguageRepositoryInterface $languageRepository,
        protected readonly Tools $tools,
        protected readonly Filesystem $filesystem,
        protected readonly FileDownloader $fileDownloader,
        protected readonly ShopConfigurationInterface $configuration,
        protected readonly TranslatorInterface $translator,
        protected readonly LoggerInterface $logger,
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
            $match = $this->productFinder->findRowMatch($row, $context);

            // database-phase defense (validation normally already skipped these
            // rows, but the DB may have changed between phases): the finder only
            // reports the identity problems as data — failing the row is THIS
            // caller's policy, with the same wording as the validator
            $reference = $row['reference'] ?? '';
            if ($match->foundOutsideShopScope) {
                $messages[] = $this->referenceOutsideShopScopeMessage($reference, $rowIndex, ImportPhaseDefinition::PHASE_DATABASE);

                return $messages;
            }
            if ($match->isAmbiguous()) {
                $messages[] = $this->ambiguousReferenceMessage($reference, $match->count(), $rowIndex, ImportPhaseDefinition::PHASE_DATABASE);

                return $messages;
            }

            $isCreation = null === $match->first();

            $productId = $this->resolveTargetProduct($row, $match, $context);

            $this->updateProductType($row, $match, $productId, $context);
            $manufacturerId = $this->resolveManufacturer($row, $rowIndex, $context, $messages);
            $this->dispatchProductUpdate($row, $rowIndex, $productId, $isCreation, $languageId, $context, $manufacturerId, $messages);
            $this->dispatchStockUpdate($row, $productId, $context);
            $this->dispatchCategories($row, $rowIndex, $productId, $context, $messages);
            $this->dispatchSuppliers($row, $rowIndex, $productId, $context, $messages);
            $this->dispatchTags($row, $productId, $isCreation, $languageId, $context);
            $this->dispatchFeatures($row, $rowIndex, $productId, $isCreation, $context, $messages);
            $this->dispatchImages($row, $rowIndex, $productId, $isCreation, $languageId, $context, $messages);
            $this->dispatchVirtualProductFile($row, $rowIndex, $productId, $messages);
            $this->dispatchCustomizationFields($row, $rowIndex, $productId, $isCreation, $languageId, $context, $messages);
            $this->dispatchSpecificPrice($row, $productId);
            $this->dispatchShops($row, $rowIndex, $productId, $context, $messages);
            $this->applyDateAdd($row, $productId, $context);
        } catch (Throwable $e) {
            // deliberate catch-all: a failing command must fail THIS ROW only
            // (structured error, remaining commands skipped), never the batch.
            // The full throwable ALWAYS goes to the log; only domain exception
            // messages reach the user (see buildRowFailureMessage()).
            $this->logger->error('Import: product row could not be fully imported', ['row' => $rowIndex, 'exception' => $e]);
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_ERROR,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->buildRowFailureMessage($e),
                $rowIndex
            );
        }

        return $messages;
    }

    /**
     * Domain and import-engine exceptions carry a message written for a human
     * (a violated business rule, an ambiguous reference...), so it is worth
     * showing. Anything else — DBAL errors, TypeError... — carries implementation
     * detail such as table and constraint names, which has no place in a
     * back-office notification and is already in the log in full.
     */
    protected function buildRowFailureMessage(Throwable $e): string
    {
        if ($e instanceof DomainException || $e instanceof ImportEngineException) {
            return $this->translator->trans('The row could not be fully imported: %error%', ['%error%' => $e->getMessage()], 'Admin.Advparameters.Notification');
        }

        return $this->translator->trans('The row could not be fully imported because of an unexpected error; see the logs for details.', [], 'Admin.Advparameters.Notification');
    }

    /**
     * @param array<string, string> $row
     */
    protected function resolveTargetProduct(array $row, FoundEntity $match, ImportRunContext $context): int
    {
        if (null !== $match->first()) {
            return $match->first();
        }

        $productType = $this->isVirtual($row) ? ProductType::TYPE_VIRTUAL : ProductType::TYPE_STANDARD;
        $localizedNames = $this->localizeForCreation($row['name'] ?? '');

        if (null !== $match->forcedId) {
            $localizedLinkRewrites = array_map(fn (string $name): string => (string) $this->tools->linkRewrite($name), $localizedNames);
            $this->productRepository->createWithForcedId($match->forcedId, $localizedNames, $localizedLinkRewrites, $productType, $context->getShopId());

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
     * The command is dispatched ONLY when the type actually changes: converting
     * a product to the type it already has is a no-op the updater would still
     * pay for with an all-shops partial update, and the conversion itself is
     * destructive (see ProductTypeUpdater::updateType() — combinations and pack
     * contents are deleted, stock reset), which the validator warns about.
     *
     * @param array<string, string> $row
     */
    protected function updateProductType(array $row, FoundEntity $match, int $productId, ImportRunContext $context): void
    {
        if (null === $match->first()) {
            return;
        }

        if (!$this->hasValue($row, 'is_virtual') || !$this->isVirtual($row)) {
            return;
        }

        if (ProductType::TYPE_VIRTUAL === $this->productRepository->getProductType(new ProductId($productId))->getValue()) {
            return;
        }

        $this->commandBus->handle(new UpdateProductTypeCommand($productId, ProductType::TYPE_VIRTUAL));
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function resolveManufacturer(array $row, int $rowIndex, ImportRunContext $context, array &$messages): ?int
    {
        $manufacturer = $row['manufacturer'] ?? '';
        if ('' === $manufacturer) {
            return null;
        }

        // a NUMERIC value is an id, never a name: creating a brand named
        // "123" from an unknown id would be nonsense, warn and drop instead
        if (ctype_digit($manufacturer)) {
            if ($this->existenceChecker->exists('manufacturer', (int) $manufacturer)) {
                return (int) $manufacturer;
            }

            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Brand with id %id% does not exist; the field will be ignored.', ['%id%' => $manufacturer], 'Admin.Advparameters.Notification'),
                $rowIndex,
                'manufacturer'
            );

            return null;
        }

        $resolved = $this->manufacturerResolver->resolve($manufacturer, $context);
        if ($resolved->isAmbiguous()) {
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Brand "%name%" matches %count% brands; the first one (id %id%) was used.', ['%name%' => $manufacturer, '%count%' => $resolved->matchCount, '%id%' => $resolved->id], 'Admin.Advparameters.Notification'),
                $rowIndex,
                'manufacturer'
            );
        }
        if ($resolved->wasCreated) {
            $messages[] = $this->autoCreationNotice($rowIndex, 'manufacturer', $this->translator->trans('Brand "%name%" did not exist and was created.', ['%name%' => $manufacturer], 'Admin.Advparameters.Notification'));
        }

        return $resolved->id;
    }

    /**
     * The import creates catalog entities the file only NAMES — brands,
     * categories, features and their values (legacy behavior, kept). That is
     * expected rather than wrong, so it is a NOTICE and not a warning: it
     * records what the run added beyond the products themselves.
     *
     * A pausing phase could not help here — by the time wasCreated is true the
     * entity exists, and the database phase never pauses. The resolvers' quiet
     * caches report wasCreated on the FIRST resolution only, so each created
     * entity is announced once per batch rather than once per row.
     */
    protected function autoCreationNotice(int $rowIndex, string $field, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_NOTICE, ImportPhaseDefinition::PHASE_DATABASE, $message, $rowIndex, $field);
    }

    /**
     * One UpdateProductCommand carrying every mapped scalar/localized field.
     *
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function dispatchProductUpdate(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context, ?int $manufacturerId, array &$messages): void
    {
        $command = new UpdateProductCommand($productId, $context->getShopConstraint());
        $hasUpdate = false;

        $localize = fn (string $value): array => $isCreation ? $this->localizeForCreation($value) : [$languageId => $value];

        // localized fields (name is only re-written on update: creation already set it)
        if (!$isCreation && $this->hasValue($row, 'name')) {
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
            if ($this->hasValue($row, $field)) {
                $command->{$setter}($localize($row[$field]));
                $hasUpdate = true;
            }
        }

        if ($this->hasValue($row, 'delivery_in_stock') || $this->hasValue($row, 'delivery_out_stock')) {
            $command->setDeliveryTimeNoteType(DeliveryTimeNoteType::TYPE_SPECIFIC);
            if ($this->hasValue($row, 'delivery_in_stock')) {
                $command->setLocalizedDeliveryTimeInStockNotes($localize($row['delivery_in_stock']));
            }
            if ($this->hasValue($row, 'delivery_out_stock')) {
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
            if ($this->hasValue($row, $field)) {
                $command->{$setter}($this->valueParser->parseBoolean($row[$field]) ?? false);
                $hasUpdate = true;
            }
        }

        // enums / plain strings
        if ($this->hasValue($row, 'visibility')) {
            $command->setVisibility($row['visibility']);
            $hasUpdate = true;
        }
        if ($this->hasValue($row, 'condition')) {
            $command->setCondition($row['condition']);
            $hasUpdate = true;
        }
        if ($this->hasValue($row, 'unity')) {
            $command->setUnity($row['unity']);
            $hasUpdate = true;
        }
        if ($this->hasValue($row, 'mpn')) {
            $command->setMpn($row['mpn']);
            $hasUpdate = true;
        }
        if ($this->hasValue($row, 'reference')) {
            $command->setReference($row['reference']);
            $hasUpdate = true;
        }
        // gtin wins over its legacy alias ean13 when both are mapped and filled
        $gtin = $this->hasValue($row, 'gtin') ? $row['gtin'] : ($row['ean13'] ?? '');
        if ('' !== $gtin) {
            $command->setGtin($gtin);
            $hasUpdate = true;
        }
        if ($this->hasValue($row, 'isbn')) {
            $command->setIsbn($row['isbn']);
            $hasUpdate = true;
        }
        if ($this->hasValue($row, 'upc')) {
            $command->setUpc($row['upc']);
            $hasUpdate = true;
        }

        // prices
        $price = $this->resolvePrice($row, $context);
        if (null !== $price) {
            $command->setPrice((string) $price);
            $hasUpdate = true;
        }
        $taxRulesGroupId = $row['id_tax_rules_group'] ?? '';
        if ('' !== $taxRulesGroupId && ctype_digit($taxRulesGroupId) && $this->existenceChecker->exists('tax_rules_group', (int) $taxRulesGroupId)) {
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
        if ($this->hasValue($row, 'ecotax')) {
            $ecotax = (bool) $this->configuration->get('PS_USE_ECOTAX', null, $context->getShopConstraint())
                ? $this->valueParser->parseDecimal($row['ecotax'])
                : new DecimalNumber('0');
            if (null !== $ecotax) {
                $command->setEcotax((string) $ecotax);
                $hasUpdate = true;
            }
        }

        // stock-related fields living on the product row
        $minimalQuantity = $this->valueParser->parseCount($row['minimal_quantity'] ?? '');
        if (null !== $minimalQuantity) {
            $command->setMinimalQuantity($minimalQuantity);
            $hasUpdate = true;
        }
        $threshold = $this->valueParser->parseInteger($row['low_stock_threshold'] ?? '');
        if (null !== $threshold) {
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
                        $this->translator->trans('The low stock alert follows the low stock level (enabled when the level is not 0); the low_stock_alert value was ignored.', [], 'Admin.Advparameters.Notification'),
                        $rowIndex,
                        'low_stock_alert'
                    );
                }
            }
        } elseif ($this->hasValue($row, 'low_stock_alert')) {
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('low_stock_alert requires a valid low_stock_threshold value; the field was ignored.', [], 'Admin.Advparameters.Notification'),
                $rowIndex,
                'low_stock_alert'
            );
        }
        if ($this->hasValue($row, 'available_date')) {
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
    protected function resolvePrice(array $row, ImportRunContext $context): ?DecimalNumber
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
        if ('' !== $taxRulesGroupId && ctype_digit($taxRulesGroupId) && $this->existenceChecker->exists('tax_rules_group', (int) $taxRulesGroupId)) {
            $price = $price->dividedBy($this->getTaxDivisor((int) $taxRulesGroupId, $context), 6);
        }

        return $price;
    }

    /**
     * (1 + rate/100) for one tax rules group, memoized: the rate is invariant
     * for the whole run (the country is fixed by getShopCountryId()), so a file
     * with one id_tax_rules_group resolves it once instead of once per row.
     */
    protected function getTaxDivisor(int $taxRulesGroupId, ImportRunContext $context): DecimalNumber
    {
        if (!isset($this->taxDivisors[$taxRulesGroupId])) {
            $rate = $this->taxComputer->getTaxRate(
                new TaxRulesGroupId($taxRulesGroupId),
                new CountryId($this->getShopCountryId($context))
            );
            $this->taxDivisors[$taxRulesGroupId] = $rate->dividedBy(new DecimalNumber('100'), 6)->plus(new DecimalNumber('1'));
        }

        return $this->taxDivisors[$taxRulesGroupId];
    }

    /**
     * Legacy Shop::getAddress() country resolution — the country whose tax
     * rate de-taxes price_tin values.
     */
    protected function getShopCountryId(ImportRunContext $context): int
    {
        $shopCountryId = (int) $this->configuration->get('PS_SHOP_COUNTRY_ID', null, $context->getShopConstraint());

        return $shopCountryId > 0 ? $shopCountryId : (int) $this->configuration->get('PS_COUNTRY_DEFAULT', null, $context->getShopConstraint());
    }

    /**
     * @param array<string, string> $row
     */
    protected function dispatchStockUpdate(array $row, int $productId, ImportRunContext $context): void
    {
        $command = new UpdateProductStockAvailableCommand($productId, $context->getShopConstraint());
        $hasUpdate = false;

        if ($this->hasValue($row, 'location')) {
            $command->setLocation($row['location']);
            $hasUpdate = true;
        }
        // strict integer parsing first: '(int) "abc"' would silently become
        // a valid enum value 0
        $outOfStock = $this->valueParser->parseInteger($row['out_of_stock'] ?? '');
        if (null !== $outOfStock && in_array($outOfStock, [0, 1, 2], true)) {
            $command->setOutOfStockType($outOfStock);
            $hasUpdate = true;
        }
        $quantity = $this->valueParser->parseInteger($row['quantity'] ?? '');
        if (null !== $quantity) {
            // the stock command only expresses deltas: read the current
            // quantity and convert the file's absolute value (delta 0 is
            // illegal and means nothing to change)
            $delta = $quantity - $this->getCurrentStockQuantity($productId, $context->getShopId());
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
    protected function dispatchCategories(array $row, int $rowIndex, int $productId, ImportRunContext $context, array &$messages): void
    {
        $categories = $row['category'] ?? '';
        if ('' === $categories) {
            return;
        }

        $ids = [];
        $languageId = $this->getLanguageId($context);
        foreach ($this->valueParser->split($categories, $context->getMultipleValueSeparator()) as $entry) {
            if (ctype_digit($entry)) {
                if ($this->existenceChecker->exists('category', (int) $entry)) {
                    $ids[] = (int) $entry;
                } else {
                    // validation already reported this as a row ERROR; reaching
                    // it here (post-write) only drops the entry, never the row
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_WARNING,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $this->translator->trans('Category with id %id% does not exist; the entry will be ignored.', ['%id%' => $entry], 'Admin.Advparameters.Notification'),
                        $rowIndex,
                        'category'
                    );
                }
                continue;
            }

            // walk the '/'-separated path from Home, one resolve-or-create
            // per segment: the id of the category the current segment was
            // found (or created) under; after the loop it holds the deepest
            // segment's own id — the one the product is associated with
            $currentCategoryId = (int) $this->configuration->get('PS_HOME_CATEGORY', null, $context->getShopConstraint());
            foreach (array_map('trim', explode('/', $entry)) as $categoryName) {
                if ('' === $categoryName) {
                    continue;
                }

                $resolvedCategory = $this->categoryResolver->resolveChild($currentCategoryId, $categoryName, $languageId, $context);
                if ($resolvedCategory->isAmbiguous()) {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_WARNING,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $this->translator->trans('Category "%name%" matches %count% sibling categories; the first one (id %id%) was used.', ['%name%' => $categoryName, '%count%' => $resolvedCategory->matchCount, '%id%' => $resolvedCategory->id], 'Admin.Advparameters.Notification'),
                        $rowIndex,
                        'category'
                    );
                }
                if ($resolvedCategory->wasCreated) {
                    $messages[] = $this->autoCreationNotice($rowIndex, 'category', $this->translator->trans('Category "%name%" did not exist and was created.', ['%name%' => $categoryName], 'Admin.Advparameters.Notification'));
                }
                $currentCategoryId = $resolvedCategory->id;
            }
            $ids[] = $currentCategoryId;
        }

        $ids = array_values(array_unique($ids));
        if ([] === $ids) {
            return;
        }

        $this->commandBus->handle(new SetAssociatedProductCategoriesCommand(
            $productId,
            $ids[0], // legacy rule: the first entry is the default category
            $ids,
            $context->getShopConstraint()
        ));
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function dispatchSuppliers(array $row, int $rowIndex, int $productId, ImportRunContext $context, array &$messages): void
    {
        $supplier = $row['supplier'] ?? '';
        if ('' === $supplier) {
            return;
        }

        $lookup = $this->supplierFinder->find($supplier, $context);
        $supplierId = $lookup->first();
        if (null === $supplierId) {
            // suppliers are never auto-created: a supplier requires an address,
            // which the import file cannot provide -> warn and drop
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Supplier "%name%" does not exist and suppliers are not auto-created by the import; the field will be ignored.', ['%name%' => $supplier], 'Admin.Advparameters.Notification'),
                $rowIndex,
                'supplier'
            );

            return;
        }
        if ($lookup->isAmbiguous()) {
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Supplier "%name%" matches %count% suppliers; the first one (id %id%) was used.', ['%name%' => $supplier, '%count%' => $lookup->count(), '%id%' => $supplierId], 'Admin.Advparameters.Notification'),
                $rowIndex,
                'supplier'
            );
        }

        // the file expresses ONE supplier, but SetSuppliersCommand replaces the
        // whole list (associateSuppliers() bulk-deletes what is missing), so the
        // row's supplier is UNIONED with the existing ones: re-importing a
        // product must never drop the suppliers the file says nothing about
        $currentAssociations = $this->getCurrentProductSuppliers($productId);
        $supplierIds = array_keys($currentAssociations);
        if (!in_array($supplierId, $supplierIds, true)) {
            $supplierIds[] = $supplierId;
        }
        $this->commandBus->handle(new SetSuppliersCommand($productId, array_values($supplierIds)));

        // same reasoning per FIELD: UpdateProductSuppliersCommand replaces the
        // association, so an unmapped/empty cell must re-send the CURRENT value
        // instead of blanking it (legacy read them off the loaded product, whose
        // fillInfo() skipped empty cells)
        $existing = $currentAssociations[$supplierId] ?? null;
        $this->commandBus->handle(new UpdateProductSuppliersCommand($productId, [
            [
                'supplier_id' => $supplierId,
                // the import file has no currency column, so an EXISTING
                // association keeps the currency it was recorded with: resetting
                // it to the shop default would reinterpret the price without
                // changing the number (100 USD silently read as 100 EUR).
                // Legacy did reset it, so this is a deliberate divergence
                'currency_id' => null !== $existing
                    ? (int) $existing['id_currency']
                    : (int) $this->configuration->get('PS_CURRENCY_DEFAULT', null, $context->getShopConstraint()),
                'reference' => $this->hasValue($row, 'supplier_reference')
                    ? $row['supplier_reference']
                    : (string) ($existing['product_supplier_reference'] ?? ''),
                'price_tax_excluded' => $this->hasValue($row, 'wholesale_price')
                    ? (string) ($this->valueParser->parseDecimal($row['wholesale_price']) ?? new DecimalNumber('0'))
                    : (string) ($existing['product_supplier_price_te'] ?? '0'),
            ],
        ]));
        $this->commandBus->handle(new SetProductDefaultSupplierCommand($productId, $supplierId));
    }

    /**
     * The product's current product-level supplier associations (no combination),
     * keyed by supplier id. Plain rows, never the legacy ObjectModel: importers
     * live in Core (getByAssociation() would hand back a ProductSupplier).
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCurrentProductSuppliers(int $productId): array
    {
        $associations = [];
        foreach ($this->productSupplierRepository->getProductSuppliersInfo(new ProductId($productId), new NoCombinationId()) as $association) {
            $associations[(int) $association['id_supplier']] = $association;
        }

        return $associations;
    }

    /**
     * @param array<string, string> $row
     */
    protected function dispatchTags(array $row, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): void
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
            foreach ($this->getAllLanguageIds() as $langId) {
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
    protected function dispatchFeatures(array $row, int $rowIndex, int $productId, bool $isCreation, ImportRunContext $context, array &$messages): void
    {
        $featuresCell = $row['features'] ?? '';
        if ('' === $featuresCell) {
            return;
        }

        $featureValues = [];
        $languageId = $this->getLanguageId($context);
        foreach ($this->valueParser->split($featuresCell, $context->getMultipleValueSeparator()) as $entry) {
            // 'Name:Value:Position[:Custom]' — position is ignored (the
            // commands manage positions); this format is the import file's,
            // so parsing it belongs here, not in the resolver
            $parts = array_map('trim', explode(':', $entry));
            $featureName = $parts[0] ?? '';
            $featureValue = $parts[1] ?? '';
            $isCustom = !empty($parts[3]);

            if ('' === $featureName || '' === $featureValue) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Invalid feature entry "%entry%" (expected Name:Value:Position[:Custom]); the entry will be ignored.', ['%entry%' => $entry], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'features'
                );
                continue;
            }

            $feature = $this->featureResolver->resolveFeature($featureName, $languageId, $context);
            if ($feature->isAmbiguous()) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Feature "%name%" matches %count% features; the first one (id %id%) was used.', ['%name%' => $featureName, '%count%' => $feature->matchCount, '%id%' => $feature->id], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'features'
                );
            }
            if ($feature->wasCreated) {
                $messages[] = $this->autoCreationNotice($rowIndex, 'features', $this->translator->trans('Feature "%name%" did not exist and was created.', ['%name%' => $featureName], 'Admin.Advparameters.Notification'));
            }

            if ($isCustom) {
                $featureValues[] = [
                    'feature_id' => $feature->id,
                    'custom_values' => $this->featureResolver->resolveCustomValues($feature->id, $featureValue, $isCreation, $isCreation ? null : $productId, $languageId),
                ];
                continue;
            }

            $value = $this->featureResolver->resolveFeatureValue($feature->id, $featureValue, $languageId);
            if ($value->isAmbiguous()) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Feature value "%value%" matches %count% values of the same feature; the first one (id %id%) was used.', ['%value%' => $featureValue, '%count%' => $value->matchCount, '%id%' => $value->id], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'features'
                );
            }
            if ($value->wasCreated) {
                $messages[] = $this->autoCreationNotice($rowIndex, 'features', $this->translator->trans('Feature value "%value%" did not exist and was created.', ['%value%' => $featureValue], 'Admin.Advparameters.Notification'));
            }
            $featureValues[] = [
                'feature_id' => $feature->id,
                'feature_value_id' => $value->id,
            ];
        }

        if ([] !== $featureValues) {
            $this->commandBus->handle(new SetProductFeatureValuesCommand($productId, $featureValues));
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function dispatchImages(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context, array &$messages): void
    {
        // parseBoolean('') is false, so an unmapped or empty cell falls through
        // without needing a hasValue() guard (same shape as isVirtual())
        if (true === $this->valueParser->parseBoolean($row['delete_existing_images'] ?? '')) {
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

        // BOTH cells are split positionally (alt N belongs to image N): empty
        // entries must be kept on either side, otherwise a hole shifts the
        // following alts onto the wrong image
        $imageUrls = $this->valueParser->splitPreservingEmpty($imagesCell, $context->getMultipleValueSeparator());
        $imageAlts = $this->valueParser->splitPreservingEmpty($row['image_alt'] ?? '', $context->getMultipleValueSeparator());

        foreach ($imageUrls as $index => $imageUrl) {
            if ('' === $imageUrl) {
                // a hole in the image cell: nothing to fetch, but the index
                // still belongs to this position so the alts stay aligned
                continue;
            }

            try {
                $temporaryFile = $this->fileDownloader->download($imageUrl);
            } catch (FileDownloadException $e) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Image "%url%" could not be fetched and was skipped: %error%', ['%url%' => $imageUrl, '%error%' => $e->getMessage()], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'image'
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
                $this->filesystem->remove($temporaryFile);
            }
        }
    }

    /**
     * A product can only ever hold ONE virtual file, so re-importing a row is an
     * UPDATE: VirtualProductUpdater::addFile() throws ALREADY_HAS_A_FILE when the
     * product already has one, and because that lands in the row catch-all it
     * would fail the whole row and get its accessories dropped in the
     * association phase.
     *
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function dispatchVirtualProductFile(array $row, int $rowIndex, int $productId, array &$messages): void
    {
        $fileUrl = $row['file_url'] ?? '';
        if ('' === $fileUrl || !$this->isVirtual($row)) {
            return;
        }

        try {
            $temporaryFile = $this->fileDownloader->download($fileUrl);
        } catch (FileDownloadException $e) {
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Virtual product file "%url%" could not be fetched and was skipped: %error%', ['%url%' => $fileUrl, '%error%' => $e->getMessage()], 'Admin.Advparameters.Notification'),
                $rowIndex,
                'file_url'
            );

            return;
        }

        try {
            $accessDays = $this->valueParser->parseCount($row['nb_days_accessible'] ?? '');
            $downloadTimesLimit = $this->valueParser->parseCount($row['nb_downloadable'] ?? '');
            $expirationDate = $this->valueParser->parseDate($row['date_expiration'] ?? '');
            $displayName = basename(parse_url($fileUrl, PHP_URL_PATH) ?: $fileUrl) ?: 'file';

            $existingFileId = $this->findExistingVirtualProductFileId($productId);
            if (null !== $existingFileId) {
                $updateCommand = new UpdateVirtualProductFileCommand($existingFileId);
                $updateCommand->setFilePath($temporaryFile);
                $updateCommand->setDisplayName($displayName);
                $updateCommand->setAccessDays($accessDays);
                $updateCommand->setDownloadTimesLimit($downloadTimesLimit);
                $updateCommand->setExpirationDate($expirationDate);
                $this->commandBus->handle($updateCommand);

                return;
            }

            $this->commandBus->handle(new AddVirtualProductFileCommand(
                $productId,
                $temporaryFile,
                $displayName,
                $accessDays,
                $downloadTimesLimit,
                $expirationDate
            ));
        } finally {
            $this->filesystem->remove($temporaryFile);
        }
    }

    /**
     * Whether the column is mapped AND carries a non-blank value.
     *
     * Deliberately NOT !empty(): "0" is a legitimate imported value (disabling a
     * boolean field, a zero price or dimension, low_stock_alert...), and !empty()
     * would silently skip those cells.
     *
     * @param array<string, string> $row
     */
    protected function hasValue(array $row, string $field): bool
    {
        return '' !== ($row[$field] ?? '');
    }

    protected function findExistingVirtualProductFileId(int $productId): ?int
    {
        return $this->virtualProductFileRepository->findIdByProductId(new ProductId($productId))?->getValue();
    }

    /**
     * uploadable_files and text_fields are integer COUNTS: the row requests
     * N file-upload fields and M text fields (the legacy stored the raw
     * counters without creating any real field — a reviewed fix). The Set
     * command replaces the product's whole field set; an explicit 0/0 on an
     * update therefore removes every existing field. Empty/unmapped cells
     * leave the product untouched.
     *
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function dispatchCustomizationFields(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context, array &$messages): void
    {
        $fileCount = $this->valueParser->parseCount($row['uploadable_files'] ?? '');
        $textCount = $this->valueParser->parseCount($row['text_fields'] ?? '');
        $customizable = true === $this->valueParser->parseBoolean($row['customizable'] ?? '');

        if (null === $fileCount && null === $textCount) {
            if ($customizable) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('"customizable" requires a number of uploadable_files/text_fields; no customization field was created.', [], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'customizable'
                );
            }

            return;
        }

        $fileCount ??= 0;
        $textCount ??= 0;

        if (0 === $fileCount && 0 === $textCount) {
            if (!$isCreation) {
                $this->commandBus->handle(new RemoveAllCustomizationFieldsFromProductCommand($productId));
            }

            return;
        }

        $fields = [];
        $fieldNumber = 0;
        foreach ([CustomizationFieldType::TYPE_FILE => $fileCount, CustomizationFieldType::TYPE_TEXT => $textCount] as $type => $count) {
            for ($i = 0; $i < $count; ++$i) {
                $label = $this->translator->trans('Customization #%number%', ['%number%' => ++$fieldNumber], 'Admin.Global');
                $fields[] = [
                    'type' => $type,
                    'localized_names' => $isCreation ? $this->localizeForCreation($label) : [$languageId => $label],
                    'is_required' => false,
                    'added_by_module' => false,
                ];
            }
        }

        $this->commandBus->handle(new SetProductCustomizationFieldsCommand($productId, $fields, $context->getShopConstraint()));
    }

    /**
     * Legacy "basic reduction": one specific price rule, all currencies/
     * countries/groups, from quantity 1. A row carrying BOTH reduction kinds
     * is ambiguous: both are dropped (the validator already warned).
     *
     * Re-importing a row is an UPDATE, not an error: the repository rejects a
     * duplicate rule (SpecificPriceConstraintException::NOT_UNIQUE_PER_PRODUCT),
     * and because that lands in the row catch-all it would fail the whole row
     * and get its accessories dropped in the association phase. So the existing
     * rule is looked up first and edited when found.
     *
     * KNOWN LIMITATION: the lookup is keyed on the rule's dates, so a row that
     * only changes reduction_from/reduction_to does not match and adds a second
     * rule. Defining what identifies "the import's basic reduction" independently
     * of its dates is a separate discussion (see PLAN.md).
     *
     * @param array<string, string> $row
     */
    protected function dispatchSpecificPrice(array $row, int $productId): void
    {
        if ($this->hasValue($row, 'reduction_price') && $this->hasValue($row, 'reduction_percent')) {
            return;
        }

        $reductionPrice = $this->valueParser->parseDecimal($row['reduction_price'] ?? '');
        $reductionPercent = $this->valueParser->parseDecimal($row['reduction_percent'] ?? '');

        if (null === $reductionPrice && null === $reductionPercent) {
            return;
        }

        $reductionType = null !== $reductionPrice ? Reduction::TYPE_AMOUNT : Reduction::TYPE_PERCENTAGE;
        $reductionValue = null !== $reductionPrice ? $reductionPrice : $reductionPercent;

        $from = $this->valueParser->parseDate($row['reduction_from'] ?? '');
        $to = $this->valueParser->parseDate($row['reduction_to'] ?? '');
        $dateTimeFrom = null !== $from ? DateTime::createFromImmutable($from) : new NullDateTime();
        $dateTimeTo = null !== $to ? DateTime::createFromImmutable($to) : new NullDateTime();

        $existingSpecificPriceId = $this->findExistingBasicSpecificPriceId($productId, $dateTimeFrom, $dateTimeTo);
        if (null !== $existingSpecificPriceId) {
            $editCommand = new EditSpecificPriceCommand($existingSpecificPriceId);
            $editCommand->setReduction($reductionType, (string) $reductionValue);
            $this->commandBus->handle($editCommand);

            return;
        }

        $this->commandBus->handle(new AddSpecificPriceCommand(
            $productId,
            $reductionType,
            (string) $reductionValue,
            true,
            '-1',
            1,
            $dateTimeFrom,
            $dateTimeTo
        ));
    }

    /**
     * The uniqueness key AddSpecificPriceCommand produces: no combination, no
     * shop, no group, no country, no currency, no customer, from quantity 1.
     * Mirroring it here is what makes the re-import idempotent.
     */
    protected function findExistingBasicSpecificPriceId(
        int $productId,
        DateTimeInterface $dateTimeFrom,
        DateTimeInterface $dateTimeTo
    ): ?int {
        $existingSpecificPriceId = $this->specificPriceRepository->findExisting(
            $productId,
            NoCombinationId::NO_COMBINATION_ID,
            NoShopId::NO_SHOP_ID,
            NoGroupId::NO_GROUP_ID,
            NoCountryId::NO_COUNTRY_ID_VALUE,
            NoCurrencyId::NO_CURRENCY_ID,
            NoCustomerId::NO_CUSTOMER_ID_VALUE,
            1,
            $dateTimeFrom->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
            $dateTimeTo->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT)
        );

        return $existingSpecificPriceId?->getValue();
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function dispatchShops(array $row, int $rowIndex, int $productId, ImportRunContext $context, array &$messages): void
    {
        $shopCell = $row['shop'] ?? '';
        if ('' === $shopCell) {
            return;
        }

        $shopIds = [];
        foreach ($this->valueParser->split($shopCell, $context->getMultipleValueSeparator()) as $entry) {
            $lookup = $this->shopFinder->find($entry, $context);
            $shopId = $lookup->first();
            if (null === $shopId) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Shop "%name%" does not exist; the entry will be ignored.', ['%name%' => $entry], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'shop'
                );
                continue;
            }
            if ($lookup->isAmbiguous()) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Shop "%name%" matches %count% shops; the first one (id %id%) was used.', ['%name%' => $entry, '%count%' => $lookup->count(), '%id%' => $shopId], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'shop'
                );
            }
            $shopIds[] = $shopId;
        }

        $shopIds = array_values(array_unique($shopIds));
        // the source shop must be part of the association (command constraint);
        // the run's shop holds the data that was just written. This also covers
        // the "every entry was dropped" case: the list becomes exactly the run's
        // shop, which the early return below then treats as nothing to do
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
    protected function applyDateAdd(array $row, int $productId, ImportRunContext $context): void
    {
        $dateAdd = $this->valueParser->parseDate($row['date_add'] ?? '');
        if (null !== $dateAdd) {
            $this->productRepository->setDateAdd($productId, $dateAdd, $context->getShopConstraint());
        }
    }

    /**
     * Current physical quantity of the product itself (no combination), 0
     * when no stock row exists yet. getForProduct() resolves shared-stock
     * setups (group-level stock rows) through the legacy shop restriction.
     */
    protected function getCurrentStockQuantity(int $productId, int $shopId): int
    {
        try {
            return (int) $this->stockAvailableRepository->getForProduct(new ProductId($productId), new ShopId($shopId))->quantity;
        } catch (StockAvailableNotFoundException) {
            return 0;
        }
    }

    /**
     * @param array<string, string> $row
     */
    protected function isVirtual(array $row): bool
    {
        return true === $this->valueParser->parseBoolean($row['is_virtual'] ?? '');
    }
}
