<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Entity\SpecificPriceRule;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\EditCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler\EditCatalogPriceRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\UpdateCatalogPriceRuleException;
use PrestaShopException;

/**
 * Handles command which edits catalog price rule handler using legacy object model
 */
final class EditCatalogPriceRuleHandler implements EditCatalogPriceRuleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditCatalogPriceRuleCommand $command)
    {
        try {
            $specificPriceRule = $this->createSpecificPriceRuleFromCommand($command);

            if (false === $specificPriceRule->validateFields(false)) {
                throw new CatalogPriceRuleException('Specific price rule contains invalid field values');
            }

            if (false === $specificPriceRule->update()) {
                throw new UpdateCatalogPriceRuleException(
                    sprintf('Failed to update specific price rule with id %s', $specificPriceRule->id)
                );
            }
            $specificPriceRule->apply();
        } catch (PrestaShopException $e) {
            throw new CatalogPriceRuleException(
                sprintf(
                    'An unexpected error occurred when editing specific price rule with id %s',
                    $command->getCatalogPriceRuleId()->getValue()
                ),
                0,
                $e
            );
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
    private function createSpecificPriceRuleFromCommand(EditCatalogPriceRuleCommand $command)
    {
        $specificPriceRule = new SpecificPriceRule($command->getCatalogPriceRuleId()->getValue());

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
        if (null !== $command->getDateFrom()) {
            $specificPriceRule->from = $command->getDateFrom();
        }
        if (null !== $command->getDateTo()) {
            $specificPriceRule->to = $command->getDateTo();
        }
        if (null !== $command->getReductionType()) {
            $specificPriceRule->reduction_type = $command->getReductionType();
        }
        if (null !== $command->isTaxIncluded()) {
            $specificPriceRule->reduction_tax = $command->isTaxIncluded();
        }
        if (null !== $command->getReduction()) {
            $specificPriceRule->reduction = $command->getReduction();
        }

        return $specificPriceRule;
    }
}
