<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\SetTaxRulesGroupStatusCommand;

/**
 * Defines contract for toggle tax rules group status handler
 */
interface ToggleTaxRulesGroupStatusHandlerInterface
{
    /**
     * @param SetTaxRulesGroupStatusCommand $command
     */
    public function handle(SetTaxRulesGroupStatusCommand $command): void;
}
