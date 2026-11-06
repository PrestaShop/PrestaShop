<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\CommandHandler;

use DateTime;
use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\EditCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler\EditCatalogPriceRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CannotUpdateCatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as UtilsDateTime;
use PrestaShopException;
use SpecificPriceRule;

/**
 * Handles command which edits catalog price rule handler using legacy object model
 */
#[AsCommandHandler]
final class EditCatalogPriceRuleHandler extends AbstractCatalogPriceRuleHandler implements EditCatalogPriceRuleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditCatalogPriceRuleCommand $command)
    {
        try {
            $specificPriceRule = $this->fetchSpecificPriceRuleFromCommand($command);

            if (false === $specificPriceRule->validateFields(false)) {
                throw new CatalogPriceRuleException('Specific price rule contains invalid field values');
            }

            if (false === $specificPriceRule->update()) {
                throw new CannotUpdateCatalogPriceRuleException(sprintf('Failed to update specific price rule with id %s', $specificPriceRule->id));
            }
            $specificPriceRule->deleteConditions();
            $specificPriceRule->apply();
        } catch (PrestaShopException $e) {
            throw new CatalogPriceRuleException(sprintf('An unexpected error occurred when editing specific price rule with id %s', $command->getCatalogPriceRuleId()->getValue()), 0, $e);
        }
    }

    /**
     * Creates SpecificPriceRule object from given command
     *
     * @param EditCatalogPriceRuleCommand $command
     *
     * @return SpecificPriceRule
     *
     * @throws PrestaShopException
     */
    private function fetchSpecificPriceRuleFromCommand(EditCatalogPriceRuleCommand $command): SpecificPriceRule
    {
        $specificPriceRule = new SpecificPriceRule($command->getCatalogPriceRuleId()->getValue());
        $this->fetchDateRange($command, $specificPriceRule);

        if (null !== $command->getName()) {
            $specificPriceRule->name = $command->getName();
        }
        if (null !== $command->getShopId()) {
            $specificPriceRule->id_shop = $command->getShopId();
        }
        if (null !== $command->getCurrencyId()) {
            $specificPriceRule->id_currency = $command->getCurrencyId();
        }
        if (null !== $command->getCountryId()) {
            $specificPriceRule->id_country = $command->getCountryId();
        }
        if (null !== $command->getGroupId()) {
            $specificPriceRule->id_group = $command->getGroupId();
        }
        if (null !== $command->getFromQuantity()) {
            $specificPriceRule->from_quantity = $command->getFromQuantity();
        }
        if (null !== $command->getPrice()) {
            $specificPriceRule->price = $command->getPrice();
        }

        if (null !== $command->isTaxIncluded()) {
            $specificPriceRule->reduction_tax = $command->isTaxIncluded();
        }
        if (null !== $command->getReduction()) {
            $specificPriceRule->reduction_type = $command->getReduction()->getType();
            $specificPriceRule->reduction = $command->getReduction()->getValue();
        }

        return $specificPriceRule;
    }

    /**
     * Fetches date range from command to object model also asserting that the range is not inverse
     *
     * @param EditCatalogPriceRuleCommand $command
     * @param SpecificPriceRule $specificPriceRule
     *
     * @throws CatalogPriceRuleConstraintException
     */
    private function fetchDateRange(EditCatalogPriceRuleCommand $command, SpecificPriceRule $specificPriceRule)
    {
        $commandDateFrom = $command->getDateTimeFrom();
        $commandDateTo = $command->getDateTimeTo();

        // range is left untouched, the stored values are not checked
        if (null === $commandDateFrom && null === $commandDateTo) {
            return;
        }

        // resulting range: updated value when provided by the command, stored value otherwise
        $dateFrom = null !== $commandDateFrom ? $commandDateFrom->format('Y-m-d H:i:s') : $specificPriceRule->from;
        $dateTo = null !== $commandDateTo ? $commandDateTo->format('Y-m-d H:i:s') : $specificPriceRule->to;

        if (!UtilsDateTime::isNull($dateFrom) && !UtilsDateTime::isNull($dateTo)) {
            $this->assertDateRangeIsNotInverse(new DateTime($dateFrom), new DateTime($dateTo));
        }

        $specificPriceRule->from = $dateFrom;
        $specificPriceRule->to = $dateTo;
    }
}
