<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\AddCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler\AddCatalogPriceRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShopException;
use SpecificPriceRule;

/**
 * Handles adding new catalog price rule using legacy object model
 */
#[AsCommandHandler]
final class AddCatalogPriceRuleHandler extends AbstractCatalogPriceRuleHandler implements AddCatalogPriceRuleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddCatalogPriceRuleCommand $command): CatalogPriceRuleId
    {
        try {
            $specificPriceRule = $this->fetchSpecificPriceRuleFromCommand($command);

            if (false === $specificPriceRule->validateFields(false)) {
                throw new CatalogPriceRuleException('Specific price rule contains invalid field values');
            }

            if (false === $specificPriceRule->add()) {
                throw new CatalogPriceRuleException(sprintf('Failed to create specific price rule'));
            }
            $specificPriceRule->deleteConditions();
            $specificPriceRule->apply();
        } catch (PrestaShopException $e) {
            throw new CatalogPriceRuleException('An unexpected error occurred while creating specific price rule', 0, $e);
        }

        return new CatalogPriceRuleId((int) $specificPriceRule->id);
    }

    /**
     * @param AddCatalogPriceRuleCommand $command
     *
     * @return SpecificPriceRule
     *
     * @throws PrestaShopException
     */
    private function fetchSpecificPriceRuleFromCommand(AddCatalogPriceRuleCommand $command): SpecificPriceRule
    {
        $specificPriceRule = new SpecificPriceRule();
        $specificPriceRule->name = $command->getName();
        $specificPriceRule->id_shop = $command->getShopId();
        $specificPriceRule->id_currency = $command->getCurrencyId();
        $specificPriceRule->id_country = $command->getCountryId();
        $specificPriceRule->id_group = $command->getGroupId();
        $specificPriceRule->from_quantity = $command->getFromQuantity();
        $specificPriceRule->price = $command->getPrice();
        $specificPriceRule->reduction_type = $command->getReduction()->getType();
        $specificPriceRule->reduction = (string) $command->getReduction()->getValue();
        $specificPriceRule->reduction_tax = $command->isTaxIncluded();

        $from = $command->getDateTimeFrom();
        $to = $command->getDateTimeTo();

        if ($from && $to) {
            $this->assertDateRangeIsNotInverse($from, $to);
        }

        if ($from) {
            $specificPriceRule->from = $from->format('Y-m-d H:i:s');
        }

        if ($to) {
            $specificPriceRule->to = $to->format('Y-m-d H:i:s');
        }

        return $specificPriceRule;
    }
}
