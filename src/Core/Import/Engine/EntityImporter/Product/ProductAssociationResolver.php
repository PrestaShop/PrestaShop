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
 *
 * None of these names carries a DB unique constraint, so a lookup can match
 * SEVERAL entities. The first one (lowest id) is used and a WARNING reports the
 * match count — an ambiguous LINK is recoverable, unlike an ambiguous product
 * identity, which fails the row (see ProductIdentityResolver). Because the
 * resolutions are cached, the warning is emitted once per run, not once per row.
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
     * @var array<string, list<int>> shop name => every matching shop id
     */
    protected array $shopCache = [];

    /**
     * @var array<int, true> feature ids whose shop association was already ensured this run
     */
    protected array $featureShopEnsured = [];

    /**
     * The run's ShopConstraint is frozen, so its concrete shop ids are constant
     * for the whole run — resolved once instead of on every auto-creation.
     *
     * @var list<int>|null
     */
    protected ?array $runShopIds = null;

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

            $ids[] = $this->resolveCategoryPath($entry, $languageId, $context, $rowIndex, $messages);
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

        $messages = [];
        $manufacturerIds = $this->manufacturerRepository->getManufacturerIdsByName($value);
        if ([] === $manufacturerIds) {
            $manufacturerId = $this->commandBus->handle(
                new AddManufacturerCommand($value, true, [], [], [], [], $this->getRunShopIds($context))
            )->getValue();
        } else {
            $manufacturerId = $manufacturerIds[0];
            if (count($manufacturerIds) > 1) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Brand "%name%" matches %count% brands; the first one (id %id%) was used.', ['%name%' => $value, '%count%' => count($manufacturerIds), '%id%' => $manufacturerId], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'manufacturer'
                );
            }
        }
        $this->manufacturerCache[$value] = $manufacturerId;

        return new ResolvedAssociation($manufacturerId, $messages);
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

        $supplierIds = $this->supplierRepository->getSupplierIdsByName($value);
        if ([] !== $supplierIds) {
            $messages = [];
            if (count($supplierIds) > 1) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Supplier "%name%" matches %count% suppliers; the first one (id %id%) was used.', ['%name%' => $value, '%count%' => count($supplierIds), '%id%' => $supplierIds[0]], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'supplier'
                );
            }

            return new ResolvedAssociation($supplierIds[0], $messages);
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

            $featureId = $this->resolveFeature($featureName, $languageId, $context, $rowIndex, $messages);

            if ($isCustom) {
                $featureValues[] = [
                    'feature_id' => $featureId,
                    'custom_values' => $this->buildCustomValues($featureId, $featureValue, $isCreation, $productId, $languageId),
                ];
                continue;
            }

            $featureValues[] = [
                'feature_id' => $featureId,
                'feature_value_id' => $this->resolveFeatureValue($featureId, $featureValue, $languageId, $rowIndex, $messages),
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
            $matchedShopIds = $this->shopCache[$entry] ??= $this->shopRepository->getShopIdsByName($entry);
            if ([] !== $matchedShopIds) {
                $shopIds[] = $matchedShopIds[0];
                if (count($matchedShopIds) > 1) {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_WARNING,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $this->translator->trans('Shop "%name%" matches %count% shops; the first one (id %id%) was used.', ['%name%' => $entry, '%count%' => count($matchedShopIds), '%id%' => $matchedShopIds[0]], 'Admin.Advparameters.Notification'),
                        $rowIndex,
                        'shop'
                    );
                }
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
     * @param list<ImportMessage> $messages
     *
     * @return int the id of the DEEPEST path segment (the last one walked) —
     *             the category the product will be associated with
     */
    protected function resolveCategoryPath(string $path, int $languageId, ImportRunContext $context, int $rowIndex, array &$messages): int
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

            $categoryIds = $this->categoryRepository->getChildCategoryIdsByName($currentCategoryId, $categoryName, $languageId, $context->getShopConstraint());
            if ([] === $categoryIds) {
                $categoryId = $this->commandBus->handle(new AddCategoryCommand(
                    $this->localizeForCreation($categoryName),
                    $this->localizeForCreation($this->dataFormatter->createFriendlyUrl($categoryName)),
                    true,
                    $currentCategoryId
                ))->getValue();
            } else {
                $categoryId = $categoryIds[0];
                if (count($categoryIds) > 1) {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_WARNING,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $this->translator->trans('Category "%name%" matches %count% sibling categories; the first one (id %id%) was used.', ['%name%' => $categoryName, '%count%' => count($categoryIds), '%id%' => $categoryId], 'Admin.Advparameters.Notification'),
                        $rowIndex,
                        'category'
                    );
                }
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

    /**
     * @param list<ImportMessage> $messages
     */
    protected function resolveFeature(string $name, int $languageId, ImportRunContext $context, int $rowIndex, array &$messages): int
    {
        if (isset($this->featureCache[$name])) {
            return $this->featureCache[$name];
        }

        $featureIds = $this->featureRepository->getFeatureIdsByName($name, $languageId);
        if ([] === $featureIds) {
            $featureId = $this->commandBus->handle(
                new AddFeatureCommand($this->localizeForCreation($name), $this->getRunShopIds($context))
            )->getValue();
        } else {
            $featureId = $featureIds[0];
            if (count($featureIds) > 1) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Feature "%name%" matches %count% features; the first one (id %id%) was used.', ['%name%' => $name, '%count%' => count($featureIds), '%id%' => $featureId], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'features'
                );
            }
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
     * entities get associated with. Memoized: the constraint cannot change
     * during a run, and this is called on every auto-creation.
     *
     * @return list<int>
     */
    protected function getRunShopIds(ImportRunContext $context): array
    {
        return $this->runShopIds ??= $this->shopRepository->getAssociatedShopIds($context->getShopConstraint());
    }

    /**
     * @param list<ImportMessage> $messages
     */
    protected function resolveFeatureValue(int $featureId, string $value, int $languageId, int $rowIndex, array &$messages): int
    {
        $cacheKey = $featureId . ':' . $value;
        if (isset($this->featureValueCache[$cacheKey])) {
            return $this->featureValueCache[$cacheKey];
        }

        $featureValueIds = $this->featureValueRepository->getFeatureValueIdsByValue($featureId, $value, $languageId);
        if ([] === $featureValueIds) {
            $featureValueId = $this->commandBus->handle(
                new AddFeatureValueCommand($featureId, $this->localizeForCreation($value))
            )->getValue();
        } else {
            $featureValueId = $featureValueIds[0];
            if (count($featureValueIds) > 1) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Feature value "%value%" matches %count% values of the same feature; the first one (id %id%) was used.', ['%value%' => $value, '%count%' => count($featureValueIds), '%id%' => $featureValueId], 'Admin.Advparameters.Notification'),
                    $rowIndex,
                    'features'
                );
            }
        }
        $this->featureValueCache[$cacheKey] = $featureValueId;

        return $featureValueId;
    }
}
