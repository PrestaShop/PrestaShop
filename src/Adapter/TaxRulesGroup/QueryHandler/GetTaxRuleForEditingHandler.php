<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\QueryHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryHandler\GetTaxRuleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRule;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\Behavior;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Handles query which gets tax rule for editing
 */
final class GetTaxRuleForEditingHandler extends AbstractTaxRulesGroupHandler implements GetTaxRuleForEditingHandlerInterface
{
    /**
     * Tax id value when no tax is selected
     */
    const NO_TAX_ID = 0;

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws TaxConstraintException
     * @throws TaxRuleNotFoundException
     * @throws TaxRulesGroupConstraintException
     * @throws TaxRuleConstraintException
     */
    public function handle(GetTaxRuleForEditing $query): EditableTaxRule
    {
        $taxRuleId = $query->getTaxRuleId();
        $taxRule = $this->getTaxRule($taxRuleId);

        $editableTaxRule = new EditableTaxRule(
            $taxRuleId,
            new TaxRulesGroupId((int) $taxRule->id_tax_rules_group),
            new CountryId((int) $taxRule->id_country),
            new Behavior($taxRule->behavior)
        );

        if (null !== $taxRule->zipcode_from) {
            $editableTaxRule->setZipCodeForm($taxRule->zipcode_from);
        }

        if (null !== $taxRule->zipcode_to) {
            $editableTaxRule->setZipCodeTo($taxRule->zipcode_to);
        }

        if (null !== $taxRule->id_state) {
            $editableTaxRule->setStateId(new StateId((int) $taxRule->id_state));
        }

        if (null !== $taxRule->id_tax && (int) $taxRule->id_tax !== self::NO_TAX_ID) {
            $editableTaxRule->setTaxId(new TaxId((int) $taxRule->id_tax));
        }

        if (null !== $taxRule->description) {
            $editableTaxRule->setDescription($taxRule->description);
        }

        return $editableTaxRule;
    }
}
