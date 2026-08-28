<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver\ManufacturerResolver;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * One UpdateProductCommand carrying every mapped scalar/localized field, plus
 * the manufacturer resolution feeding it (the manufacturer id has no other
 * consumer, so its resolve-or-create lives here rather than crossing steps).
 */
class ProductFieldsStep extends AbstractProductRowStep
{
    use LocalizedValueTrait;

    /**
     * @var array<int, DecimalNumber> memoized (1 + rate/100) per tax rules group
     */
    protected array $taxDivisors = [];

    public function __construct(
        ValueParser $valueParser,
        protected readonly ManufacturerResolver $manufacturerResolver,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
        protected readonly TaxComputer $taxComputer,
        protected readonly ShopConfigurationInterface $configuration,
        protected readonly LanguageRepositoryInterface $languageRepository,
        protected readonly CommandBusInterface $commandBus,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        // no cheap row-only guard: the step reads ~40 columns, emits warnings
        // even without dispatching (low_stock_alert) and gates the command on
        // $hasUpdate itself
        return true;
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $messages = [];
        $manufacturerId = $this->resolveManufacturer($row, $rowIndex, $context, $messages);

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
                        [$rowIndex],
                        'low_stock_alert'
                    );
                }
            }
        } elseif ($this->hasValue($row, 'low_stock_alert')) {
            $messages[] = new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('low_stock_alert requires a valid low_stock_threshold value; the field was ignored.', [], 'Admin.Advparameters.Notification'),
                [$rowIndex],
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

        return $messages;
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
                [$rowIndex],
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
                [$rowIndex],
                'manufacturer'
            );
        }
        if ($resolved->wasCreated) {
            $messages[] = $this->autoCreationNotice($rowIndex, 'manufacturer', $this->translator->trans('Brand "%name%" did not exist and was created.', ['%name%' => $manufacturer], 'Admin.Advparameters.Notification'));
        }

        return $resolved->id;
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
}
