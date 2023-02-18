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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShopException;
use TaxRulesGroup;

/**
 * Provides common methods for tax rules group handlers
 */
abstract class AbstractTaxRulesGroupHandler extends AbstractObjectModelHandler
{
    /**
     * Gets legacy TaxRuleGroup object
     *
     * @param TaxRulesGroupId $taxRulesGroupId
     *
     * @return TaxRulesGroup
     *
     * @throws TaxRulesGroupNotFoundException
     */
    protected function getTaxRulesGroup(TaxRulesGroupId $taxRulesGroupId): TaxRulesGroup
    {
        $taxRulesGroupIdValue = $taxRulesGroupId->getValue();

        try {
            $taxRulesGroup = new TaxRulesGroup($taxRulesGroupIdValue);
        } catch (PrestaShopException $e) {
            throw new TaxRulesGroupNotFoundException(sprintf('Tax rules group with id "%s" was not found.', $taxRulesGroupIdValue));
        }

        if ($taxRulesGroup->id !== $taxRulesGroupIdValue) {
            throw new TaxRulesGroupNotFoundException(sprintf('Tax rules group with id "%s" was not found.', $taxRulesGroupIdValue));
        }

        return $taxRulesGroup;
    }

    /**
     * Deletes legacy TaxRulesGroup
     *
     * @param TaxRulesGroup $taxRulesGroup
     *
     * @return bool
     *
     * @throws CannotDeleteTaxRulesGroupException
     */
    protected function deleteTaxRulesGroup(TaxRulesGroup $taxRulesGroup): bool
    {
        try {
            return $taxRulesGroup->delete();
        } catch (PrestaShopException $e) {
            throw new CannotDeleteTaxRulesGroupException(sprintf('An error occurred when deleting tax rules group object with id "%s".', $taxRulesGroup->id));
        }
    }

    /**
     * Set legacy tax rules group status
     *
     * @param TaxRulesGroup $taxRulesGroup
     * @param bool $newStatus
     *
     * @return bool
     *
     * @throws TaxRulesGroupException
     */
    protected function setTaxRulesGroupStatus(TaxRulesGroup $taxRulesGroup, bool $newStatus)
    {
        $taxRulesGroup->active = $newStatus;

        try {
            return $taxRulesGroup->save();
        } catch (PrestaShopException $e) {
            throw new TaxRulesGroupException(sprintf('An error occurred when updating tax rules group status with id "%s"', $taxRulesGroup->id));
        }
    }
}
