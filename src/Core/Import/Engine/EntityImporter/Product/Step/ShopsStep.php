<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\ShopFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Associates the product with the shops named in the shop cell. Runs
 * near-last: SetProductShopsCommand propagates the run shop's product row to
 * the other shops, so every write scoped to the run's shop must have happened
 * before it.
 */
class ShopsStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly ShopFinder $shopFinder,
        protected readonly CommandBusInterface $commandBus,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'shop');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $messages = [];
        $shopCell = $row['shop'] ?? '';

        $shopIds = [];
        foreach ($this->valueParser->split($shopCell, $context->getMultipleValueSeparator()) as $entry) {
            $lookup = $this->shopFinder->find($entry, $context);
            $shopId = $lookup->first();
            if (null === $shopId) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Shop "%name%" does not exist; the entry will be ignored.', ['%name%' => $entry], 'Admin.Advparameters.Notification'),
                    [$rowIndex],
                    'shop'
                );
                continue;
            }
            if ($lookup->isAmbiguous()) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Shop "%name%" matches %count% shops; the first one (id %id%) was used.', ['%name%' => $entry, '%count%' => $lookup->count(), '%id%' => $shopId], 'Admin.Advparameters.Notification'),
                    [$rowIndex],
                    'shop'
                );
            }
            $shopIds[] = $shopId;
        }

        $shopIds = array_values(array_unique($shopIds));
        // the source shop must be part of the association (command constraint);
        // the run's shop holds the data that was just written. This also covers
        // the "every entry was dropped" case: the list becomes exactly the run's
        // shop, which the early return below then treats as nothing to do
        if (!in_array($context->getShopId(), $shopIds, true)) {
            $shopIds[] = $context->getShopId();
        }

        if ([$context->getShopId()] === $shopIds) {
            return $messages;
        }

        $this->commandBus->handle(new SetProductShopsCommand($productId, $context->getShopId(), $shopIds));

        return $messages;
    }
}
