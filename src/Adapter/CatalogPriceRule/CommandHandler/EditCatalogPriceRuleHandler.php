<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

        $modelDateFrom = $specificPriceRule->from;
        $modelDateTo = $specificPriceRule->to;

        //if `date from` value is being updated
        if (null !== $commandDateFrom) {
            //and if `date to` is set in database
            if (!UtilsDateTime::isNull($modelDateTo)) {
                //asserts that range between these values is not inverse
                $this->assertDateRangeIsNotInverse($commandDateFrom, new DateTime($modelDateTo));
            }

            $specificPriceRule->from = $commandDateFrom->format('Y-m-d H:i:s');
        }

        //if `date to` value is being updated
        if (null !== $commandDateTo) {
            //and if `date from` is set in database
            if (UtilsDateTime::isNull($modelDateFrom)) {
                //asserts that range between these values is not inverse
                $this->assertDateRangeIsNotInverse(new DateTime($modelDateFrom), $commandDateTo);
            }

            $specificPriceRule->to = $commandDateTo->format('Y-m-d H:i:s');
        }
    }
}
