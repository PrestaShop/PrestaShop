<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        } catch (PrestaShopException) {
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
        } catch (PrestaShopException) {
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
        } catch (PrestaShopException) {
            throw new TaxRulesGroupException(sprintf('An error occurred when updating tax rules group status with id "%s"', $taxRulesGroup->id));
        }
    }
}
