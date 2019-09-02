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

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Command responsible for multiple tax rules groups status toggling
 */
class BulkToggleTaxRulesGroupStatusCommand
{
    /**
     * @var TaxRulesGroupId[]
     */
    private $taxRulesGroupIds = [];

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param array $taxRulesGroupIds
     * @param bool $expectedStatus
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function __construct(array $taxRulesGroupIds, bool $expectedStatus)
    {
        $this->expectedStatus = $expectedStatus;
        $this->setTaxRulesGroupIds($taxRulesGroupIds);
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }

    /**
     * @return TaxRulesGroupId[]
     */
    public function getTaxRulesGroupIds(): array
    {
        return $this->taxRulesGroupIds;
    }

    /**
     * @param int[] $taxRulesGroupIds
     *
     * @throws TaxRulesGroupConstraintException
     */
    private function setTaxRulesGroupIds(array $taxRulesGroupIds)
    {
        foreach ($taxRulesGroupIds as $taxRulesGroupId) {
            $this->taxRulesGroupIds[] = new TaxRulesGroupId($taxRulesGroupId);
        }
    }
}
