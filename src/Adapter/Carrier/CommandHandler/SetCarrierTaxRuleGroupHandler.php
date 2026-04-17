<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierTaxRuleGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\SetCarrierTaxRuleGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

#[AsCommandHandler]
class SetCarrierTaxRuleGroupHandler implements SetCarrierTaxRuleGroupHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
        private readonly TaxRulesGroupRepository $taxRulesGroupRepository,
    ) {
    }

    public function handle(SetCarrierTaxRuleGroupCommand $command): CarrierId
    {
        if ($command->getCarrierTaxRuleGroupId()->getValue() !== 0) {
            $this->taxRulesGroupRepository->assertTaxRulesGroupExists($command->getCarrierTaxRuleGroupId());
        }

        $newCarrier = $this->carrierRepository->getEditableOrNewVersion($command->getCarrierId());
        $newCarrierId = new CarrierId($newCarrier->id);
        $this->carrierRepository->setTaxRulesGroup($newCarrierId, $command->getCarrierTaxRuleGroupId(), $command->getShopConstraint());

        return $newCarrierId;
    }
}
