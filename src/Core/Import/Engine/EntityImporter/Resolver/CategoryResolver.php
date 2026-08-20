<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;

/**
 * Resolves ONE child category by name under a given parent, creating it when
 * nothing matches (legacy Category::searchByPath parity, via
 * AddCategoryCommand) — callers walk '/'-separated paths by chaining calls,
 * which keeps the segment name in their hands when a segment is ambiguous.
 * Numeric ids are whole-entry values, a MATCH-ONLY concern the caller probes
 * through ImportEntityExistenceChecker.
 *
 * Resolutions are cached for the service lifetime (one batch request) in
 * their QUIET form: creation/ambiguity information is returned once, on first
 * resolution, so callers emit each warning once per run, not once per row.
 */
class CategoryResolver
{
    use LocalizedValueTrait;

    /**
     * @var array<string, ResolvedEntity> quiet resolutions, keyed '<parent id>:<name>'
     */
    protected array $cache = [];

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly ImportDataFormatter $dataFormatter,
        protected readonly LanguageRepositoryInterface $languageRepository,
    ) {
    }

    public function resolveChild(int $parentCategoryId, string $name, int $languageId, ImportRunContext $context): ResolvedEntity
    {
        $cacheKey = $parentCategoryId . ':' . $name;
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $categoryIds = $this->categoryRepository->getChildCategoryIdsByName($parentCategoryId, $name, $languageId, $context->getShopConstraint());
        if ([] === $categoryIds) {
            $categoryId = $this->commandBus->handle(new AddCategoryCommand(
                $this->localizeForCreation($name),
                $this->localizeForCreation($this->dataFormatter->createFriendlyUrl($name)),
                true,
                $parentCategoryId
            ))->getValue();
            $resolved = new ResolvedEntity($categoryId, true);
        } else {
            $resolved = new ResolvedEntity($categoryIds[0], false, count($categoryIds));
        }
        $this->cache[$cacheKey] = new ResolvedEntity($resolved->id);

        return $resolved;
    }
}
