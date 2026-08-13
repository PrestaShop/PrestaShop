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
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ResolvedAssociation;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
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
class ProductAssociationResolver
{
    use LocalizedValueTrait;

    /**
     * @var array<string, int>
     */
    protected array $categoryCache = [];

    /**
     * @var array<string, int>
     */
    protected array $manufacturerCache = [];

    /**
     * @var array<string, int>
     */
    protected array $featureCache = [];

    /**
     * @var array<string, int>
     */
    protected array $featureValueCache = [];

    /**
     * @var array<int, true> feature ids whose shop association was already ensured this run
     */
    protected array $featureShopEnsured = [];

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly ValueParser $valueParser,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
        protected readonly ManufacturerRepository $manufacturerRepository,
        protected readonly SupplierRepository $supplierRepository,
        protected readonly ShopRepository $shopRepository,
        protected readonly LanguageRepositoryInterface $languageRepository,
        protected readonly FeatureRepository $featureRepository,
        protected readonly FeatureValueRepository $featureValueRepository,
        protected readonly ShopConfigurationInterface $configuration,
        protected readonly ImportDataFormatter $dataFormatter,
        protected readonly TranslatorInterface $translator,
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

            $ids[] = $this->resolveCategoryPath($entry, $languageId, $context);
        }

        return ['ids' => array_values(array_unique($ids)), 'messages' => $messages];
    }

    public function resolveManufacturer(string $value, ImportRunContext $context, int $rowIndex): ResolvedAssociation
    {
        // a NUMERIC value is an id, never a name: creating a brand named
        // "123" from an unknown id would be nonsense, warn and drop instead
        if (ctype_digit($value)) {
            if ($this->existenceChecker->exists('manufacturer', (int) $value)) {
                return new ResolvedAssociation((int) $value);
            }

            return new ResolvedAssociation(null, [
                new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Brand with id %id% does not exist; the field will be ignored.', ['%id%' => $value], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'manufacturer'
                ),
            ]);
        }

        if (isset($this->manufacturerCache[$value])) {
            return new ResolvedAssociation($this->manufacturerCache[$value]);
        }

        $manufacturerId = $this->manufacturerRepository->getManufacturerIdByName($value);
        if (null === $manufacturerId) {
            $manufacturerId = $this->commandBus->handle(
                new AddManufacturerCommand($value, true, [], [], [], [], $this->getRunShopIds($context))
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
        if (ctype_digit($value) && $this->existenceChecker->exists('supplier', (int) $value)) {
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
     * manage positions. Custom values follow the single-language-file rule of
     * every localized field: duplicated into all languages on creation, only
     * the file's language on update (the other languages are re-sent as-is —
     * see buildCustomValues()).
     *
     * @param list<string> $entries
     * @param int|null $productId the row's product on update (custom values need its current texts), null on creation
     *
     * @return array{featureValues: list<array<string, mixed>>, messages: list<ImportMessage>}
     */
    public function resolveFeatures(array $entries, ImportRunContext $context, int $rowIndex, bool $isCreation, ?int $productId = null): array
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
                    'custom_values' => $this->buildCustomValues($featureId, $featureValue, $isCreation, $productId, $languageId),
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
     * Resolves the shop cell (ids or names). Unresolvable entries — unknown
     * names AND unknown numeric ids — produce a warning and are dropped; an
     * empty result falls back to the run's shop.
     *
     * @return array{shopIds: list<int>, messages: list<ImportMessage>}
     */
    public function resolveShops(string $cell, ImportRunContext $context, int $rowIndex): array
    {
        $shopIds = [];
        $messages = [];

        foreach ($this->valueParser->split($cell, $context->getMultipleValueSeparator()) as $entry) {
            if (ctype_digit($entry)) {
                if ($this->existenceChecker->exists('shop', (int) $entry)) {
                    $shopIds[] = (int) $entry;
                } else {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_WARNING,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $this->translator->trans('Shop "%name%" does not exist; the entry will be ignored.', ['%name%' => $entry], 'Admin.Advparameters.Notification'),
                        $rowIndex,
                        'shop'
                    );
                }
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
    protected function resolveCategoryPath(string $path, int $languageId, ImportRunContext $context): int
    {
        if (isset($this->categoryCache[$path])) {
            return $this->categoryCache[$path];
        }

        // the id of the category the current segment was found (or created)
        // under; after the loop it holds the deepest segment's own id
        $currentCategoryId = (int) $this->configuration->get('PS_HOME_CATEGORY', null, $context->getShopConstraint());
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

            $categoryId = $this->categoryRepository->getChildCategoryIdByName($currentCategoryId, $categoryName, $languageId, $context->getShopConstraint());
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

    /**
     * The single-language-file rule for CUSTOM feature values: on creation
     * the file's text goes into every language; on update only the file's
     * language changes. SetProductFeatureValuesCommand replaces the custom
     * value row wholesale (and ObjectModel refills missing languages from
     * the default one), so "keeping" the other languages means reading the
     * current texts and re-sending them merged with the new one.
     *
     * @return array<int, string> language id => value
     */
    protected function buildCustomValues(int $featureId, string $value, bool $isCreation, ?int $productId, int $languageId): array
    {
        if (!$isCreation && null !== $productId) {
            $existingTexts = $this->featureValueRepository->getProductCustomFeatureValueTexts($featureId, $productId);
            if ([] !== $existingTexts) {
                $existingTexts[$languageId] = $value;

                return $existingTexts;
            }
        }

        return $this->localizeForCreation($value);
    }

    protected function resolveFeature(string $name, int $languageId, ImportRunContext $context): int
    {
        if (isset($this->featureCache[$name])) {
            return $this->featureCache[$name];
        }

        $featureId = $this->featureRepository->getFeatureIdByName($name, $languageId);
        if (null === $featureId) {
            $featureId = $this->commandBus->handle(
                new AddFeatureCommand($this->localizeForCreation($name), $this->getRunShopIds($context))
            )->getValue();
        } else {
            $this->ensureFeatureShopAssociation($featureId, $context);
        }
        $this->featureCache[$name] = $featureId;

        return $featureId;
    }

    /**
     * SetProductFeatureValuesCommand writes feature_product but never
     * feature_shop, while every feature read INNER JOINs feature_shop: a
     * feature REUSED from another shop would make the imported values
     * invisible on the run's shops. Ensure the association instead — a
     * feature is never duplicated per shop (see the name lookup above, which
     * is deliberately global).
     */
    protected function ensureFeatureShopAssociation(int $featureId, ImportRunContext $context): void
    {
        if (isset($this->featureShopEnsured[$featureId])) {
            return;
        }
        $this->featureShopEnsured[$featureId] = true;

        $currentShopIds = array_map('intval', $this->featureRepository->get(new FeatureId($featureId))->getAssociatedShops());
        if ([] === array_diff($this->getRunShopIds($context), $currentShopIds)) {
            return;
        }

        $command = new EditFeatureCommand($featureId);
        $command->setAssociatedShopIds(array_values(array_unique(array_merge($currentShopIds, $this->getRunShopIds($context)))));
        $this->commandBus->handle($command);
    }

    /**
     * The concrete shops of the run's frozen scope — what auto-created
     * entities get associated with.
     *
     * @return list<int>
     */
    protected function getRunShopIds(ImportRunContext $context): array
    {
        return $this->shopRepository->getAssociatedShopIds($context->getShopConstraint());
    }

    protected function resolveFeatureValue(int $featureId, string $value, int $languageId): int
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
}
