<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver\CategoryResolver;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Associates the product with the categories named by the category cell:
 * numeric entries are existing ids, name entries are '/'-separated paths
 * walked from Home with one resolve-or-create per segment.
 */
class CategoriesStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
        protected readonly CategoryResolver $categoryResolver,
        protected readonly ShopConfigurationInterface $configuration,
        protected readonly CommandBusInterface $commandBus,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'category');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $messages = [];
        $categories = $row['category'] ?? '';

        $ids = [];
        foreach ($this->valueParser->split($categories, $context->getMultipleValueSeparator()) as $entry) {
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
                        [$rowIndex],
                        'category'
                    );
                }
                continue;
            }

            // walk the '/'-separated path from Home, one resolve-or-create
            // per segment: the id of the category the current segment was
            // found (or created) under; after the loop it holds the deepest
            // segment's own id — the one the product is associated with
            $currentCategoryId = (int) $this->configuration->get('PS_HOME_CATEGORY', null, $context->getShopConstraint());
            foreach (array_map('trim', explode('/', $entry)) as $categoryName) {
                if ('' === $categoryName) {
                    continue;
                }

                $resolvedCategory = $this->categoryResolver->resolveChild($currentCategoryId, $categoryName, $languageId, $context);
                if ($resolvedCategory->isAmbiguous()) {
                    $messages[] = new ImportMessage(
                        ImportMessage::SEVERITY_WARNING,
                        ImportPhaseDefinition::PHASE_DATABASE,
                        $this->translator->trans('Category "%name%" matches %count% sibling categories; the first one (id %id%) was used.', ['%name%' => $categoryName, '%count%' => $resolvedCategory->matchCount, '%id%' => $resolvedCategory->id], 'Admin.Advparameters.Notification'),
                        [$rowIndex],
                        'category'
                    );
                }
                if ($resolvedCategory->wasCreated) {
                    $messages[] = $this->autoCreationNotice($rowIndex, 'category', $this->translator->trans('Category "%name%" did not exist and was created.', ['%name%' => $categoryName], 'Admin.Advparameters.Notification'));
                }
                $currentCategoryId = $resolvedCategory->id;
            }
            $ids[] = $currentCategoryId;
        }

        $ids = array_values(array_unique($ids));
        if ([] === $ids) {
            return $messages;
        }

        $this->commandBus->handle(new SetAssociatedProductCategoriesCommand(
            $productId,
            $ids[0], // legacy rule: the first entry is the default category
            $ids,
            $context->getShopConstraint()
        ));

        return $messages;
    }
}
