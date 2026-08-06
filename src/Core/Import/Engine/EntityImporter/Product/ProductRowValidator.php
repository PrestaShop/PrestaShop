<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
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
final class ProductRowValidator
{
    private const REFERENCE_MAX_LENGTH = 64;
    private const REFERENCE_PATTERN = '/^[^<>;={}]*$/u';
    private const GTIN_PATTERN = '/^[0-9]{0,14}$/';
    private const UPC_PATTERN = '/^[0-9]{0,12}$/';
    private const ISBN_MAX_LENGTH = 32;

    private const DECIMAL_FIELDS = ['price_tex', 'price_tin', 'wholesale_price', 'unit_price', 'ecotax', 'additional_shipping_cost', 'reduction_price', 'reduction_percent'];
    private const DIMENSION_FIELDS = ['width', 'height', 'depth', 'weight'];
    private const DATE_FIELDS = ['available_date', 'date_add', 'reduction_from', 'reduction_to', 'date_expiration'];
    private const BOOLEAN_FIELDS = ['active', 'on_sale', 'online_only', 'available_for_order', 'show_price', 'delete_existing_images', 'is_virtual', 'customizable', 'low_stock_alert'];
    private const INTEGER_FIELDS = ['quantity', 'minimal_quantity', 'low_stock_threshold', 'nb_downloadable', 'nb_days_accessible'];
    /** Non-negative integer COUNTS of customization fields to create (not booleans) */
    private const CUSTOMIZATION_COUNT_FIELDS = ['uploadable_files', 'text_fields'];

    public function __construct(
        private readonly ValueParser $valueParser,
        private readonly ProductIdentityResolver $identityResolver,
        private readonly CategoryRepository $categoryRepository,
        private readonly TaxRulesGroupRepository $taxRulesGroupRepository,
        private readonly TranslatorInterface $translator,
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

        $match = $this->identityResolver->resolve($row, $context);
        if (!$match->isUpdate() && '' === ($row['name'] ?? '')) {
            $messages[] = $this->error($rowIndex, 'name', $this->translator->trans('The name is required when creating a product.', [], 'Admin.Advparameters.Notification'));
        }

        $this->validateFormats($row, $rowIndex, $messages);
        $this->validateEnums($row, $rowIndex, $messages);
        $this->validateNumbers($row, $rowIndex, $messages);
        $this->validateDatesAndBooleans($row, $rowIndex, $messages);
        $this->validateCategories($row, $rowIndex, $context, $messages);
        $this->validateTaxRulesGroup($row, $rowIndex, $messages);

        return $messages;
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function validateFormats(array $row, int $rowIndex, array &$messages): void
    {
        $reference = $row['reference'] ?? '';
        if ('' !== $reference && (mb_strlen($reference) > self::REFERENCE_MAX_LENGTH || !preg_match(self::REFERENCE_PATTERN, $reference))) {
            $messages[] = $this->error($rowIndex, 'reference', $this->translator->trans('Invalid reference "%value%".', ['%value%' => $reference], 'Admin.Advparameters.Notification'));
        }

        // gtin and its legacy alias ean13 share the same storage and pattern
        foreach (['gtin', 'ean13'] as $gtinField) {
            $gtin = $row[$gtinField] ?? '';
            if ('' !== $gtin && !preg_match(self::GTIN_PATTERN, $gtin)) {
                $messages[] = $this->error($rowIndex, $gtinField, $this->translator->trans('Invalid GTIN "%value%".', ['%value%' => $gtin], 'Admin.Advparameters.Notification'));
            }
        }

        $upc = $row['upc'] ?? '';
        if ('' !== $upc && !preg_match(self::UPC_PATTERN, $upc)) {
            $messages[] = $this->error($rowIndex, 'upc', $this->translator->trans('Invalid UPC "%value%".', ['%value%' => $upc], 'Admin.Advparameters.Notification'));
        }

        $isbn = $row['isbn'] ?? '';
        if (mb_strlen($isbn) > self::ISBN_MAX_LENGTH) {
            $messages[] = $this->error($rowIndex, 'isbn', $this->translator->trans('Invalid ISBN "%value%".', ['%value%' => $isbn], 'Admin.Advparameters.Notification'));
        }

        $id = $row['id'] ?? '';
        if ('' !== $id && !ctype_digit($id)) {
            $messages[] = $this->error($rowIndex, 'id', $this->translator->trans('Invalid id "%value%", expected a positive number.', ['%value%' => $id], 'Admin.Advparameters.Notification'));
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function validateEnums(array $row, int $rowIndex, array &$messages): void
    {
        $visibility = $row['visibility'] ?? '';
        if ('' !== $visibility && !in_array($visibility, ProductVisibility::AVAILABLE_VISIBILITY_VALUES, true)) {
            $messages[] = $this->error($rowIndex, 'visibility', $this->translator->trans('Invalid visibility "%value%".', ['%value%' => $visibility], 'Admin.Advparameters.Notification'));
        }

        $condition = $row['condition'] ?? '';
        if ('' !== $condition && !in_array($condition, ProductCondition::AVAILABLE_CONDITIONS, true)) {
            $messages[] = $this->error($rowIndex, 'condition', $this->translator->trans('Invalid condition "%value%".', ['%value%' => $condition], 'Admin.Advparameters.Notification'));
        }

        $outOfStock = $row['out_of_stock'] ?? '';
        if ('' !== $outOfStock && !in_array((int) $outOfStock, [OutOfStockType::OUT_OF_STOCK_NOT_AVAILABLE, OutOfStockType::OUT_OF_STOCK_AVAILABLE, OutOfStockType::OUT_OF_STOCK_DEFAULT], true)) {
            $messages[] = $this->warning($rowIndex, 'out_of_stock', $this->translator->trans('Invalid out-of-stock action "%value%", the field will be ignored.', ['%value%' => $outOfStock], 'Admin.Advparameters.Notification'));
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function validateNumbers(array $row, int $rowIndex, array &$messages): void
    {
        foreach (self::DECIMAL_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && null === $this->valueParser->parseDecimal($value)) {
                $messages[] = $this->error($rowIndex, $field, $this->translator->trans('Invalid number "%value%".', ['%value%' => $value], 'Admin.Advparameters.Notification'));
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
                $messages[] = $this->error($rowIndex, $field, $this->translator->trans('Invalid number "%value%".', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            } elseif ($decimal->isNegative()) {
                $messages[] = $this->error($rowIndex, $field, $this->translator->trans('"%field%" cannot be negative.', ['%field%' => $field], 'Admin.Advparameters.Notification'));
            }
        }

        foreach (self::INTEGER_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && !preg_match('/^-?[0-9]+$/', $value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid number "%value%", the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }

        foreach (self::CUSTOMIZATION_COUNT_FIELDS as $field) {
            $value = $row[$field] ?? '';
            if ('' !== $value && !preg_match('/^[0-9]+$/', $value)) {
                $messages[] = $this->warning($rowIndex, $field, $this->translator->trans('Invalid count "%value%" (expected a number of customization fields, 0 or more), the field will be ignored.', ['%value%' => $value], 'Admin.Advparameters.Notification'));
            }
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function validateDatesAndBooleans(array $row, int $rowIndex, array &$messages): void
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
    private function validateCategories(array $row, int $rowIndex, ImportRunContext $context, array &$messages): void
    {
        $categories = $row['category'] ?? '';
        if ('' === $categories) {
            return;
        }

        foreach ($this->valueParser->split($categories, $context->getMultipleValueSeparator()) as $entry) {
            if (ctype_digit($entry) && !$this->categoryExists((int) $entry)) {
                $messages[] = $this->error($rowIndex, 'category', $this->translator->trans('Category with id %id% does not exist.', ['%id%' => $entry], 'Admin.Advparameters.Notification'));
            }
        }
    }

    /**
     * @param array<string, string> $row
     * @param list<ImportMessage> $messages
     */
    private function validateTaxRulesGroup(array $row, int $rowIndex, array &$messages): void
    {
        $taxRulesGroupId = $row['id_tax_rules_group'] ?? '';
        if ('' === $taxRulesGroupId) {
            return;
        }

        if (!ctype_digit($taxRulesGroupId) || !$this->taxRulesGroupExists((int) $taxRulesGroupId)) {
            $messages[] = $this->warning($rowIndex, 'id_tax_rules_group', $this->translator->trans('Tax rules group %id% does not exist, the field will be ignored (tax rules groups are never auto-created).', ['%id%' => $taxRulesGroupId], 'Admin.Advparameters.Notification'));
        }
    }

    private function categoryExists(int $categoryId): bool
    {
        if ($categoryId <= 0) {
            return false;
        }

        try {
            $this->categoryRepository->assertCategoryExists(new CategoryId($categoryId));
        } catch (CategoryNotFoundException) {
            return false;
        }

        return true;
    }

    /**
     * Soft-deleted (historized) groups are treated as absent — assigning one
     * would resurrect it on the product.
     */
    private function taxRulesGroupExists(int $taxRulesGroupId): bool
    {
        if ($taxRulesGroupId <= 0) {
            return false;
        }

        try {
            $taxRulesGroup = $this->taxRulesGroupRepository->get(new TaxRulesGroupId($taxRulesGroupId));
        } catch (TaxRulesGroupNotFoundException) {
            return false;
        }

        return !(bool) $taxRulesGroup->deleted;
    }

    private function error(int $rowIndex, string $field, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_VALIDATION, $message, $rowIndex, $field);
    }

    private function warning(int $rowIndex, string $field, string $message): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, $message, $rowIndex, $field);
    }
}
