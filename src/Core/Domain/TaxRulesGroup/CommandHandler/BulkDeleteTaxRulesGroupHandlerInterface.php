<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkDeleteTaxRulesGroupCommand;

/**
 * Defines contract for bulk delete tax rules group handler
 */
interface BulkDeleteTaxRulesGroupHandlerInterface
{
    /**
     * @param BulkDeleteTaxRulesGroupCommand $command
     */
    public function handle(BulkDeleteTaxRulesGroupCommand $command): void;
}
