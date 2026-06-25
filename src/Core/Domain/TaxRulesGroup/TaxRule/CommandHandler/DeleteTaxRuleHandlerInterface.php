<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\DeleteTaxRuleCommand;

/**
 * Defines contract for deleting a tax rule
 */
interface DeleteTaxRuleHandlerInterface
{
    /**
     * @param DeleteTaxRuleCommand $command
     */
    public function handle(DeleteTaxRuleCommand $command): void;
}
