<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\AddTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandResult\AddTaxRuleResult;

/**
 * Defines contract for adding a tax rule
 */
interface AddTaxRuleHandlerInterface
{
    /**
     * @param AddTaxRuleCommand $command
     *
     * @return AddTaxRuleResult
     */
    public function handle(AddTaxRuleCommand $command): AddTaxRuleResult;
}
