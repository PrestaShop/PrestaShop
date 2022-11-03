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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\AddCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler\AddCatalogPriceRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShopException;
use SpecificPriceRule;

/**
 * Handles adding new catalog price rule using legacy object model
 */
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
