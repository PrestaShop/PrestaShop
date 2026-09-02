<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkSetTaxRulesGroupStatusCommand;

/**
 * Defines contract for bulk toggle tax rules group status handler
 */
interface BulkToggleTaxRulesGroupStatusHandlerInterface
{
    /**
     * @param BulkSetTaxRulesGroupStatusCommand $command
     */
    public function handle(BulkSetTaxRulesGroupStatusCommand $command): void;
}
