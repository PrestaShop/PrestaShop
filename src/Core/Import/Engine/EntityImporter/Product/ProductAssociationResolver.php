<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\CategoryLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\FeatureLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\LanguageLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ManufacturerLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ShopLookup;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\SupplierLookup;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Resolves the row's association cells (category paths, manufacturer,
 * supplier, features, shops) into ids, auto-creating what the per-field
 * policy allows: categories (per missing path level), manufacturers,
 * features and feature values are auto-created; suppliers are NOT
 * (warn-and-drop — a supplier cannot be created from a name alone);
 * unknown NUMERIC category ids are validation errors, never stubs.
 *
 * Name-to-id resolutions are cached for the lifetime of the service
 * (one batch request), so a 1000-row file creates each missing
 * category/manufacturer/feature once.
 */
final class ProductAssociationResolver
{
    /**
     * @var array<string, int>
     */
    private array $categoryCache = [];

    /**
     * @var array<string, int>
     */
    private array $manufacturerCache = [];

    /**
     * @var array<string, int>
     */
    private array $featureCache = [];

    /**
     * @var array<string, int>
     */
    private array $featureValueCache = [];

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly CategoryLookup $categoryLookup,
        private readonly ManufacturerLookup $manufacturerLookup,
        private readonly SupplierLookup $supplierLookup,
        private readonly ShopLookup $shopLookup,
        private readonly LanguageLookup $languageLookup,
        private readonly FeatureLookup $featureLookup,
        private readonly ImportDataFormatter $dataFormatter,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Resolves category entries (numeric id, name, or '/'-separated path),
     * auto-creating missing path levels under Home.
     *
     * @param list<string> $entries
     *
     * @return array{ids: list<int>, messages: list<ImportMessage>}
     */
    public function resolveCategories(array $entries, ImportRunContext $context, int $rowIndex): array
    {
        $ids = [];
        $messages = [];
        $languageId = $this->getLanguageId($context);

        foreach ($entries as $entry) {
            if (ctype_digit($entry)) {
                if ($this->categoryLookup->categoryExists((int) $entry)) {
                    $ids[] = (int) $entry;
                } else {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_ERROR,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $rowIndex,
                        'category',
                        $this->translator->trans('Category with id %id% does not exist.', ['%id%' => $entry], 'Admin.Advparameters.Notification')
                    );
                }
                continue;
            }

            $ids[] = $this->resolveCategoryPath($entry, $languageId);
        }

        return ['ids' => array_values(array_unique($ids)), 'messages' => $messages];
    }

    public function resolveManufacturer(string $value, ImportRunContext $context): ResolvedAssociation
    {
        if (ctype_digit($value) && $this->manufacturerLookup->manufacturerExists((int) $value)) {
            return new ResolvedAssociation((int) $value);
        }

        if (isset($this->manufacturerCache[$value])) {
            return new ResolvedAssociation($this->manufacturerCache[$value]);
        }

        $manufacturerId = $this->manufacturerLookup->getManufacturerIdByName($value);
        if (null === $manufacturerId) {
            $manufacturerId = $this->commandBus->handle(
                new AddManufacturerCommand($value, true, [], [], [], [], [$context->getShopId()])
            )->getValue();
        }
        $this->manufacturerCache[$value] = $manufacturerId;

        return new ResolvedAssociation($manufacturerId);
    }

    /**
     * Suppliers are never auto-created (a supplier requires an address, which
     * the import file cannot provide): unknown supplier -> warn and drop.
     */
    public function resolveSupplier(string $value, int $rowIndex): ResolvedAssociation
    {
        if (ctype_digit($value) && $this->supplierLookup->supplierExists((int) $value)) {
            return new ResolvedAssociation((int) $value);
        }

        $supplierId = $this->supplierLookup->getSupplierIdByName($value);
        if (null !== $supplierId) {
            return new ResolvedAssociation($supplierId);
        }

        return new ResolvedAssociation(null, [
            new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $rowIndex,
                'supplier',
                $this->translator->trans('Supplier "%name%" does not exist and suppliers are not auto-created by the import; the field will be ignored.', ['%name%' => $value], 'Admin.Advparameters.Notification')
            ),
        ]);
    }

    /**
     * Parses 'Name:Value:Position[:Custom]' feature entries, auto-creating
     * missing features and feature values. Position is ignored: the commands
     * manage positions.
     *
     * @param list<string> $entries
     *
     * @return array{featureValues: list<array<string, mixed>>, messages: list<ImportMessage>}
     */
    public function resolveFeatures(array $entries, ImportRunContext $context, int $rowIndex): array
    {
        $featureValues = [];
        $messages = [];
        $languageId = $this->getLanguageId($context);

        foreach ($entries as $entry) {
            $parts = array_map('trim', explode(':', $entry));
            $featureName = $parts[0] ?? '';
            $featureValue = $parts[1] ?? '';
            $isCustom = !empty($parts[3]);

            if ('' === $featureName || '' === $featureValue) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $rowIndex,
                    'features',
                    $this->translator->trans('Invalid feature entry "%entry%" (expected Name:Value:Position[:Custom]); the entry will be ignored.', ['%entry%' => $entry], 'Admin.Advparameters.Notification')
                );
                continue;
            }

            $featureId = $this->resolveFeature($featureName, $languageId, $context);

            if ($isCustom) {
                $featureValues[] = [
                    'feature_id' => $featureId,
                    'custom_values' => $this->localizeForCreation($featureValue),
                ];
                continue;
            }

            $featureValues[] = [
                'feature_id' => $featureId,
                'feature_value_id' => $this->resolveFeatureValue($featureId, $featureValue, $languageId),
            ];
        }

        return ['featureValues' => $featureValues, 'messages' => $messages];
    }

    /**
     * Resolves the shop cell (ids or names). Unresolvable entries produce a
     * warning and are dropped; an empty result falls back to the run's shop.
     *
     * @return array{shopIds: list<int>, messages: list<ImportMessage>}
     */
    public function resolveShops(string $cell, ImportRunContext $context, int $rowIndex): array
    {
        $shopIds = [];
        $messages = [];

        $entries = array_map('trim', explode(',', $cell));
        foreach ($entries as $entry) {
            if ('' === $entry) {
                continue;
            }
            if (ctype_digit($entry)) {
                $shopIds[] = (int) $entry;
                continue;
            }
            $shopId = $this->shopLookup->getShopIdByName($entry);
            if (null !== $shopId) {
                $shopIds[] = $shopId;
            } else {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $rowIndex,
                    'shop',
                    $this->translator->trans('Shop "%name%" does not exist; the entry will be ignored.', ['%name%' => $entry], 'Admin.Advparameters.Notification')
                );
            }
        }

        if ([] === $shopIds) {
            $shopIds[] = $context->getShopId();
        }

        return ['shopIds' => array_values(array_unique($shopIds)), 'messages' => $messages];
    }

    /**
     * Walks a '/'-separated category path from Home, creating each missing
     * level (legacy Category::searchByPath parity, via AddCategoryCommand).
     */
    private function resolveCategoryPath(string $path, int $languageId): int
    {
        if (isset($this->categoryCache[$path])) {
            return $this->categoryCache[$path];
        }

        $parentId = $this->categoryLookup->getHomeCategoryId();
        $walkedPath = '';
        foreach (array_map('trim', explode('/', $path)) as $level) {
            if ('' === $level) {
                continue;
            }
            $walkedPath .= ('' === $walkedPath ? '' : '/') . $level;

            if (isset($this->categoryCache[$walkedPath])) {
                $parentId = $this->categoryCache[$walkedPath];
                continue;
            }

            $categoryId = $this->categoryLookup->getChildCategoryIdByName($parentId, $level, $languageId);
            if (null === $categoryId) {
                $categoryId = $this->commandBus->handle(new AddCategoryCommand(
                    $this->localizeForCreation($level),
                    $this->localizeForCreation($this->dataFormatter->createFriendlyUrl($level)),
                    true,
                    $parentId
                ))->getValue();
            }

            $this->categoryCache[$walkedPath] = $categoryId;
            $parentId = $categoryId;
        }

        return $parentId;
    }

    private function resolveFeature(string $name, int $languageId, ImportRunContext $context): int
    {
        if (isset($this->featureCache[$name])) {
            return $this->featureCache[$name];
        }

        $featureId = $this->featureLookup->getFeatureIdByName($name, $languageId);
        if (null === $featureId) {
            $featureId = $this->commandBus->handle(
                new AddFeatureCommand($this->localizeForCreation($name), [$context->getShopId()])
            )->getValue();
        }
        $this->featureCache[$name] = $featureId;

        return $featureId;
    }

    private function resolveFeatureValue(int $featureId, string $value, int $languageId): int
    {
        $cacheKey = $featureId . ':' . $value;
        if (isset($this->featureValueCache[$cacheKey])) {
            return $this->featureValueCache[$cacheKey];
        }

        $featureValueId = $this->featureLookup->getFeatureValueIdByValue($featureId, $value, $languageId);
        if (null === $featureValueId) {
            $featureValueId = $this->commandBus->handle(
                new AddFeatureValueCommand($featureId, $this->localizeForCreation($value))
            )->getValue();
        }
        $this->featureValueCache[$cacheKey] = $featureValueId;

        return $featureValueId;
    }

    /**
     * Duplicates a value into every installed language (creation rule for
     * single-language import files).
     *
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
