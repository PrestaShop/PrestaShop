<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\DeleteTaxRulesGroupCommand;

/**
 * Defines contract for delete tax rules group handler
 */
interface DeleteTaxRulesGroupHandlerInterface
{
    /**
     * @param DeleteTaxRulesGroupCommand $command
     */
    public function handle(DeleteTaxRulesGroupCommand $command): void;
}
