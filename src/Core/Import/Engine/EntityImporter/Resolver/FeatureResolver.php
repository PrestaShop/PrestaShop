<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;

/**
 * Resolves feature NAMES and predefined feature VALUES to ids, auto-creating
 * what is missing (parsing the 'Name:Value:Position[:Custom]' cell entries is
 * the product-import file format, i.e. the caller's concern). Custom values
 * follow the single-language-file rule of every localized field: duplicated
 * into all languages on creation, only the file's language on update (the
 * other languages are re-sent as-is — see resolveCustomValues()).
 *
 * Caching and once-per-batch reporting come from QuietResolutionTrait; the two
 * lookups share its cache under the 'feature:' and 'value:' key prefixes.
 */
class FeatureResolver
{
    use LocalizedValueTrait;
    use QuietResolutionTrait;

    /**
     * @var array<int, true> feature ids whose shop association was already ensured this run
     */
    protected array $featureShopEnsured = [];

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly FeatureRepository $featureRepository,
        protected readonly FeatureValueRepository $featureValueRepository,
        protected readonly LanguageRepositoryInterface $languageRepository,
        protected readonly RunShopIdsProvider $runShopIdsProvider,
    ) {
    }

    public function resolveFeature(string $name, int $languageId, ImportRunContext $context): ResolvedEntity
    {
        $resolved = $this->resolveThroughCache(
            'feature:' . $name,
            fn (): array => $this->featureRepository->getFeatureIdsByName($name, $languageId),
            function () use ($name, $context): int {
                $featureId = $this->commandBus->handle(
                    new AddFeatureCommand($this->localizeForCreation($name), $this->runShopIdsProvider->getRunShopIds($context))
                )->getValue();
                // created WITH the run's shops, so there is nothing to ensure
                $this->featureShopEnsured[$featureId] = true;

                return $featureId;
            }
        );

        if (!isset($this->featureShopEnsured[$resolved->id])) {
            $this->ensureFeatureShopAssociation($resolved->id, $context);
        }

        return $resolved;
    }

    public function resolveFeatureValue(int $featureId, string $value, int $languageId): ResolvedEntity
    {
        return $this->resolveThroughCache(
            'value:' . $featureId . ':' . $value,
            fn (): array => $this->featureValueRepository->getFeatureValueIdsByValue($featureId, $value, $languageId),
            fn (): int => $this->commandBus->handle(
                new AddFeatureValueCommand($featureId, $this->localizeForCreation($value))
            )->getValue()
        );
    }

    /**
     * SetProductFeatureValuesCommand writes feature_product but never
     * feature_shop, while every feature read INNER JOINs feature_shop: a
     * feature REUSED from another shop would make the imported values
     * invisible on the run's shops. Ensure the association instead — a
     * feature is never duplicated per shop (the name lookup is deliberately
     * global).
     */
    protected function ensureFeatureShopAssociation(int $featureId, ImportRunContext $context): void
    {
        if (isset($this->featureShopEnsured[$featureId])) {
            return;
        }
        $this->featureShopEnsured[$featureId] = true;

        $currentShopIds = array_map('intval', $this->featureRepository->get(new FeatureId($featureId))->getAssociatedShops());
        $runShopIds = $this->runShopIdsProvider->getRunShopIds($context);
        if ([] === array_diff($runShopIds, $currentShopIds)) {
            return;
        }

        $command = new EditFeatureCommand($featureId);
        $command->setAssociatedShopIds(array_values(array_unique(array_merge($currentShopIds, $runShopIds))));
        $this->commandBus->handle($command);
    }

    /**
     * The single-language-file rule for CUSTOM feature values: on creation
     * the file's text goes into every language; on update only the file's
     * language changes. SetProductFeatureValuesCommand replaces the custom
     * value row wholesale (and ObjectModel refills missing languages from
     * the default one), so "keeping" the other languages means reading the
     * current texts and re-sending them merged with the new one.
     *
     * @param int|null $productId the row's product on update (its current texts are re-sent), null on creation
     *
     * @return array<int, string> language id => value
     */
    public function resolveCustomValues(int $featureId, string $value, bool $isCreation, ?int $productId, int $languageId): array
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
}
