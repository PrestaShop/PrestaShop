<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ResolvedAssociation;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
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
    use LocalizedValueTrait;

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
        private readonly CategoryRepository $categoryRepository,
        private readonly ManufacturerRepository $manufacturerRepository,
        private readonly SupplierRepository $supplierRepository,
        private readonly ShopRepository $shopRepository,
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly FeatureRepository $featureRepository,
        private readonly FeatureValueRepository $featureValueRepository,
        private readonly ConfigurationInterface $configuration,
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
                if ($this->categoryExists((int) $entry)) {
                    $ids[] = (int) $entry;
                } else {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_ERROR,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $this->translator->trans('Category with id %id% does not exist.', ['%id%' => $entry], 'Admin.Advparameters.Notification'),
                        $rowIndex,
                        'category'
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
        if (ctype_digit($value) && $this->manufacturerExists((int) $value)) {
            return new ResolvedAssociation((int) $value);
        }

        if (isset($this->manufacturerCache[$value])) {
            return new ResolvedAssociation($this->manufacturerCache[$value]);
        }

        $manufacturerId = $this->manufacturerRepository->getManufacturerIdByName($value);
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
        if (ctype_digit($value) && $this->supplierExists((int) $value)) {
            return new ResolvedAssociation((int) $value);
        }

        $supplierId = $this->supplierRepository->getSupplierIdByName($value);
        if (null !== $supplierId) {
            return new ResolvedAssociation($supplierId);
        }

        return new ResolvedAssociation(null, [
            new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Supplier "%name%" does not exist and suppliers are not auto-created by the import; the field will be ignored.', ['%name%' => $value], 'Admin.Advparameters.Notification'),
                $rowIndex,
                'supplier'
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
                    $this->translator->trans('Invalid feature entry "%entry%" (expected Name:Value:Position[:Custom]); the entry will be ignored.', ['%entry%' => $entry], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'features'
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
            $shopId = $this->shopRepository->getShopIdByName($entry);
            if (null !== $shopId) {
                $shopIds[] = $shopId;
            } else {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Shop "%name%" does not exist; the entry will be ignored.', ['%name%' => $entry], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'shop'
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
     * Path segments are ALWAYS names: numeric ids are whole-entry values,
     * handled by resolveCategories() before the walk.
     *
     * @return int the id of the DEEPEST path segment (the last one walked) —
     *             the category the product will be associated with
     */
    private function resolveCategoryPath(string $path, int $languageId): int
    {
        if (isset($this->categoryCache[$path])) {
            return $this->categoryCache[$path];
        }

        // the id of the category the current segment was found (or created)
        // under; after the loop it holds the deepest segment's own id
        $currentCategoryId = (int) $this->configuration->get('PS_HOME_CATEGORY');
        $walkedPath = '';
        foreach (array_map('trim', explode('/', $path)) as $categoryName) {
            if ('' === $categoryName) {
                continue;
            }
            $walkedPath .= ('' === $walkedPath ? '' : '/') . $categoryName;

            if (isset($this->categoryCache[$walkedPath])) {
                $currentCategoryId = $this->categoryCache[$walkedPath];
                continue;
            }

            $categoryId = $this->categoryRepository->getChildCategoryIdByName($currentCategoryId, $categoryName, $languageId);
            if (null === $categoryId) {
                $categoryId = $this->commandBus->handle(new AddCategoryCommand(
                    $this->localizeForCreation($categoryName),
                    $this->localizeForCreation($this->dataFormatter->createFriendlyUrl($categoryName)),
                    true,
                    $currentCategoryId
                ))->getValue();
            }

            $this->categoryCache[$walkedPath] = $categoryId;
            $currentCategoryId = $categoryId;
        }

        return $currentCategoryId;
    }

    private function resolveFeature(string $name, int $languageId, ImportRunContext $context): int
    {
        if (isset($this->featureCache[$name])) {
            return $this->featureCache[$name];
        }

        $featureId = $this->featureRepository->getFeatureIdByName($name, $languageId);
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

        $featureValueId = $this->featureValueRepository->getFeatureValueIdByValue($featureId, $value, $languageId);
        if (null === $featureValueId) {
            $featureValueId = $this->commandBus->handle(
                new AddFeatureValueCommand($featureId, $this->localizeForCreation($value))
            )->getValue();
        }
        $this->featureValueCache[$cacheKey] = $featureValueId;

        return $featureValueId;
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

    private function manufacturerExists(int $manufacturerId): bool
    {
        if ($manufacturerId <= 0) {
            return false;
        }

        try {
            $this->manufacturerRepository->assertManufacturerExists(new ManufacturerId($manufacturerId));
        } catch (ManufacturerNotFoundException) {
            return false;
        }

        return true;
    }

    private function supplierExists(int $supplierId): bool
    {
        if ($supplierId <= 0) {
            return false;
        }

        try {
            $this->supplierRepository->assertSupplierExists(new SupplierId($supplierId));
        } catch (SupplierNotFoundException) {
            return false;
        }

        return true;
    }
}
