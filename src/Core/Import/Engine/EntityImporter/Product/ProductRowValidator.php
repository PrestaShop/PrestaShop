<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Gtin;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\FoundEntity;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\ProductFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validation-phase row checks. Performs NO writes — it cheaply mirrors the
 * constraints of the value objects the database phase will feed, so that
 * whole-file feedback exists before anything is persisted.
 *
 * ERROR means the row will be skipped by the later phases; WARNING means the
 * offending field will be dropped or defaulted but the row goes through.
 */
class ProductRowValidator
{
    use ProductIdentityMessagesTrait;

    protected const DECIMAL_FIELDS = ['price_tex', 'price_tin', 'wholesale_price', 'unit_price', 'ecotax', 'additional_shipping_cost', 'reduction_price', 'reduction_percent'];
    protected const DIMENSION_FIELDS = ['width', 'height', 'depth', 'weight'];
    protected const DATE_FIELDS = ['available_date', 'date_add', 'reduction_from', 'reduction_to', 'date_expiration'];
    protected const BOOLEAN_FIELDS = ['active', 'on_sale', 'online_only', 'available_for_order', 'show_price', 'delete_existing_images', 'is_virtual', 'customizable', 'low_stock_alert'];
    protected const INTEGER_FIELDS = ['quantity', 'low_stock_threshold'];
    /** Non-negative integer counts — a negative value is meaningless and the field is ignored, mirroring the database phase */
    protected const COUNT_FIELDS = ['minimal_quantity', 'nb_days_accessible', 'nb_downloadable'];
    /** Non-negative integer COUNTS of customization fields to create (not booleans) */
    protected const CUSTOMIZATION_COUNT_FIELDS = ['text_fields', 'uploadable_files'];

    public function __construct(
        protected readonly ValueParser $valueParser,
        protected readonly ProductFinder $productFinder,
        protected readonly ProductRepository $productRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
        protected readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param array<string, string> $row mapped row values
     *
     * @return list<ImportMessage>
     */
    public function validate(array $row, int $rowIndex, ImportRunContext $context): array
    {
        $messages = [];

        $match = $this->productFinder->findRowMatch($row, $context);

        // the finder only reports these identity problems as data; turning
        // them into ROW ERRORS is this validator's policy: creating would
        // duplicate a reference living on another shop, and updating an
        // arbitrary one of several homonyms is destructive (an ambiguous
        // association LINK only warns — see the row importer)
        $reference = $row['reference'] ?? '';
        if ($match->foundOutsideShopScope) {
            return [$this->referenceOutsideShopScopeMessage($reference, $rowIndex, ImportPhaseDefinition::PHASE_VALIDATION)];
        }
        if ($match->isAmbiguous()) {
            return [$this->ambiguousReferenceMessage($reference, $match->count(), $rowIndex, ImportPhaseDefinition::PHASE_VALIDATION)];
        }

        if (null === $match->first() && '' === ($row['name'] ?? '')) {
            $messages[] = $this->error($rowIndex, 'name', $this->translator->trans('The name is required when creating a product.', [], 'Admin.Advparameters.Notification'));
        }

        $this->validateFormats($row, $rowIndex, $context, $messages);
        $this->validateEnums($row, $rowIndex, $messages);
        $this->validateNumbers($row, $rowIndex, $messages);
        $this->validateDatesAndBooleans($row, $rowIndex, $messages);
        $this->validateCategories($row, $rowIndex, $context, $messages);
        $this->validateTaxRulesGroup($row, $rowIndex, $messages);
        $this->validateProductTypeChange($row, $rowIndex, $match, $messages);

        return $messages;
    }

    /**
     * Converting an existing product to a virtual product is DESTRUCTIVE for
     * some source types: ProductTypeUpdater::updateType() deletes every
     * combination and resets the stock, or empties the pack contents. The row
     * still goes through — the conversion is what the file asked for — but the
     * validation phase is pausing, so warning here is what lets the merchant
     * cancel the run before anything is written.
     *
     * The impact sentences are the ones the back office already shows in its
     * type-switch modal (HeaderType), reused verbatim.
     *
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function validateProductTypeChange(array $row, int $rowIndex, FoundEntity $match, array &$messages): void
    {
        $productId = $match->first();
        if (null === $productId || true !== $this->valueParser->parseBoolean($row['is_virtual'] ?? '')) {
            return;
        }

        $impacts = match ($this->productRepository->getProductType(new ProductId($productId))->getValue()) {
            ProductType::TYPE_COMBINATIONS => [
                $this->translator->trans('This will delete all combinations.', [], 'Admin.Catalog.Notification'),
                $this->translator->trans('This will reset the stock of this product.', [], 'Admin.Catalog.Notification'),
            ],
            ProductType::TYPE_PACK => [
                $this->translator->trans('This will delete the list of products in this pack.', [], 'Admin.Catalog.Notification'),
            ],
            default => [],
        };

        if ([] === $impacts) {
            return;
        }

        $messages[] = $this->warning(
            $rowIndex,
            'is_virtual',
            implode(' ', [
                $this->translator->trans('is_virtual will convert this product to a virtual product.', [], 'Admin.Advparameters.Notification'),
                ...$impacts,
            ])
        );
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function validateFormats(array $row, int $rowIndex, ImportRunContext $context, array &$messages): void
    {
        // constraints mirrored from the VOs the database-phase commands will
        // build, so a bad value is a clear validation error instead of a
        // generic row failure later
        $reference = $row['reference'] ?? '';
        if ('' !== $reference && (strlen($reference) > Reference::MAX_LENGTH || !preg_match(Reference::VALID_PATTERN, $reference))) {
            $messages[] = $this->error($rowIndex, 'reference', $this->translator->trans('Invalid reference "%value%".', ['%value%' => $reference], 'Admin.Advparameters.Notification'));
        }

        // gtin and its legacy alias ean13 share the same storage (both cells
        // feed setGtin()), hence the same Gtin constraints
        foreach (['gtin', 'ean13'] as $gtinField) {
            $gtin = $row[$gtinField] ?? '';
            if ('' !== $gtin && (strlen($gtin) > Gtin::MAX_LENGTH || !preg_match(Gtin::VALID_PATTERN, $gtin))) {
                $messages[] = $this->error($rowIndex, $gtinField, $this->translator->trans('Invalid GTIN "%value%".', ['%value%' => $gtin], 'Admin.Advparameters.Notification'));
            }
        }

        $upc = $row['upc'] ?? '';
        if ('' !== $upc && (strlen($upc) > Upc::MAX_LENGTH || !preg_match(Upc::VALID_PATTERN, $upc))) {
            $messages[] = $this->error($rowIndex, 'upc', $this->translator->trans('Invalid UPC "%value%".', ['%value%' => $upc], 'Admin.Advparameters.Notification'));
        }

        // the Isbn VO validates the full ISBN-10/13 format but accepts the
        // empty string — mirrored by the '' guard
        $isbn = $row['isbn'] ?? '';
        if ('' !== $isbn && (strlen($isbn) > Isbn::MAX_LENGTH || !preg_match(Isbn::VALID_PATTERN, $isbn))) {
            $messages[] = $this->error($rowIndex, 'isbn', $this->translator->trans('Invalid ISBN "%value%".', ['%value%' => $isbn], 'Admin.Advparameters.Notification'));
        }

        // without force IDs the id column is ignored entirely, so a malformed
        // value must not fail the row
        $id = $row['id'] ?? '';
        if ($context->getOptions()->forceIds && '' !== $id && !ctype_digit($id)) {
            $messages[] = $this->error($rowIndex, 'id', $this->translator->trans('Invalid id "%value%", expected a positive number.', ['%value%' => $id], 'Admin.Advparameters.Notification'));
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function validateEnums(array $row, int $rowIndex, array &$messages): void
    {
        $visibility = $row['visibility'] ?? '';
        if ('' !== $visibility && !in_array($visibility, ProductVisibility::AVAILABLE_VISIBILITY_VALUES, true)) {
            $messages[] = $this->error($rowIndex, 'visibility', $this->translator->trans('Invalid visibility "%value%".', ['%value%' => $visibility], 'Admin.Advparameters.Notification'));
        }

        $condition = $row['condition'] ?? '';
        if ('' !== $condition && !in_array($condition, ProductCondition::AVAILABLE_CONDITIONS, true)) {
            $messages[] = $this->error($rowIndex, 'condition', $this->translator->trans('Invalid condition "%value%".', ['%value%' => $condition], 'Admin.Advparameters.Notification'));
        }

        // strict integer parsing first: '(int) "abc"' would silently pass as
        // the valid enum value 0
        $outOfStock = $row['out_of_stock'] ?? '';
        if ('' !== $outOfStock) {
            $parsedOutOfStock = $this->valueParser->parseInteger($outOfStock);
            if (null === $parsedOutOfStock || !in_array($parsedOutOfStock, [OutOfStockType::OUT_OF_STOCK_NOT_AVAILABLE, OutOfStockType::OUT_OF_STOCK_AVAILABLE, OutOfStockType::OUT_OF_STOCK_DEFAULT], true)) {
                $messages[] = $this->warning($rowIndex, 'out_of_stock', $this->translator->trans('Invalid out-of-stock action "%value%", the field will be ignored.', ['%value%' => $outOfStock], 'Admin.Advparameters.Notification'));
            }
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function validateNumbers(array $row, int $rowIndex, array &$messages): void
    {
        // an unparseable number drops the FIELD, not the row: the rest of the
        // row is still worth importing, and the database phase ignores the cell
        // the same way (parseDecimal() returning null simply skips the setter).
        // "1,234.56" lands here — the thousands separator is not supported
        foreach (self::DECIMAL_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && null === $this->valueParser->parseDecimal($value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid number "%value%", the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }

        // a row cannot carry both reduction kinds: there is no way to guess
        // which one was intended, both are dropped by the database phase
        if ('' !== ($row['reduction_price'] ?? '') && '' !== ($row['reduction_percent'] ?? '')) {
            $messages[] = $this->warning($rowIndex, 'reduction_price', $this->translator->trans('Both reduction_price and reduction_percent are set; the two fields are mutually exclusive so both will be ignored.', [], 'Admin.Advparameters.Notification'));
        }

        $reductionPercent = $row['reduction_percent'] ?? '';
        if ('' !== $reductionPercent) {
            $percent = $this->valueParser->parseDecimal($reductionPercent);
            if (null !== $percent && $percent->isGreaterThan(new DecimalNumber('100'))) {
                $messages[] = $this->error($rowIndex, 'reduction_percent', $this->translator->trans('A discount percentage cannot exceed 100.', [], 'Admin.Advparameters.Notification'));
            }
        }

        foreach (self::DIMENSION_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' === $value) {
                continue;
            }
            $decimal = $this->valueParser->parseDecimal($value);
            if (null === $decimal) {
                // unparseable: the database phase skips the setter, so only the
                // field is lost. A NEGATIVE value below is different — it IS
                // passed to the command, which rejects it and fails the row
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid number "%value%", the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            } elseif ($decimal->isNegative()) {
                $messages[] = $this->error($rowIndex, $field, $this->translator->trans('"%field%" cannot be negative.', ['%field%' => $field], 'Admin.Advparameters.Notification'));
            }
        }

        foreach (self::INTEGER_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && null === $this->valueParser->parseInteger($value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid number "%value%", the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }

        foreach (self::COUNT_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && null === $this->valueParser->parseCount($value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid number "%value%", the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }

        foreach (self::CUSTOMIZATION_COUNT_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && null === $this->valueParser->parseCount($value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid count "%value%" (expected a number of customization fields, 0 or more), the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function validateDatesAndBooleans(array $row, int $rowIndex, array &$messages): void
    {
        foreach (self::DATE_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && null === $this->valueParser->parseDate($value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid date "%value%" (expected yyyy-mm-dd), the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }

        foreach (self::BOOLEAN_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && null === $this->valueParser->parseBoolean($value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Unrecognized boolean "%value%" (expected 0/1/true/false/yes/no), "false" will be used.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }
    }

    /**
     * Unknown numeric category ids are errors: the legacy behavior of creating
     * a stub category with that forced id under Home was a reviewed trap.
     *
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function validateCategories(array $row, int $rowIndex, ImportRunContext $context, array &$messages): void
    {
        $categories = $row['category'] ?? '';
        if ('' === $categories) {
            return;
        }

        foreach ($this->valueParser->split($categories, $context->getMultipleValueSeparator()) as $entry) {
            if (ctype_digit($entry) && !$this->existenceChecker->exists('category', (int) $entry)) {
                $messages[] = $this->error($rowIndex, 'category', $this->translator->trans('Category with id %id% does not exist.', ['%id%' => $entry], 'Admin.Advparameters.Notification'));
            }
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    protected function validateTaxRulesGroup(array $row, int $rowIndex, array &$messages): void
    {
        $taxRulesGroupId = $row['id_tax_rules_group'] ?? '';
        if ('' === $taxRulesGroupId) {
            return;
        }

        if (!ctype_digit($taxRulesGroupId) || !$this->existenceChecker->exists('tax_rules_group', (int) $taxRulesGroupId)) {
            $messages[] = $this->warning($rowIndex, 'id_tax_rules_group', $this->translator->trans('Tax rules group %id% does not exist, the field will be ignored (tax rules groups are never auto-created).', ['%id%' => $taxRulesGroupId], 'Admin.Advparameters.Notification'));
        }
    }

    protected function error(int $rowIndex, string $field, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_VALIDATION, $message, $rowIndex, $field);
    }

    protected function warning(int $rowIndex, string $field, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, $message, $rowIndex, $field);
    }
}
