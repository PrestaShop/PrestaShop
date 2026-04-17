<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierTaxRuleGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

interface SetCarrierTaxRuleGroupHandlerInterface
{
    public function handle(SetCarrierTaxRuleGroupCommand $command): CarrierId;
}
