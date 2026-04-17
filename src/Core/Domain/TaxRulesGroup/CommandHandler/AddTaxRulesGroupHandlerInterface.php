<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Defines contract for add tax rules group handler
 */
interface AddTaxRulesGroupHandlerInterface
{
    /**
     * @param AddTaxRulesGroupCommand $command
     *
     * @return TaxRulesGroupId
     */
    public function handle(AddTaxRulesGroupCommand $command): TaxRulesGroupId;
}
