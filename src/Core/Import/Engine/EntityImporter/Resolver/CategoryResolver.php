<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
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
 * Caching and once-per-batch reporting come from QuietResolutionTrait.
 */
class CategoryResolver
{
    use LocalizedValueTrait;
    use QuietResolutionTrait;

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly Tools $tools,
        protected readonly LanguageRepositoryInterface $languageRepository,
    ) {
    }

    public function resolveChild(int $parentCategoryId, string $name, int $languageId, ImportRunContext $context): ResolvedEntity
    {
        return $this->resolveThroughCache(
            $parentCategoryId . ':' . $name,
            fn (): array => $this->categoryRepository->getChildCategoryIdsByName($parentCategoryId, $name, $languageId, $context->getShopConstraint()),
            fn (): int => $this->commandBus->handle(new AddCategoryCommand(
                $this->localizeForCreation($name),
                $this->localizeForCreation((string) $this->tools->linkRewrite($name)),
                true,
                $parentCategoryId
            ))->getValue()
        );
    }
}
