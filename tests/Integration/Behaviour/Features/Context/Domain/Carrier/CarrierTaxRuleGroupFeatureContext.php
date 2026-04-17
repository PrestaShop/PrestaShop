<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain\Carrier;

use Carrier;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierTaxRuleGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

class CarrierTaxRuleGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I set tax rule :taxRulesGroupReference for carrier :reference
     */
    public function editTaxRuleWithoutIdUpdate(string $reference, string $taxRulesGroupReference): void
    {
        $initialCarrierId = $this->getSharedStorage()->get($reference);
        $carrierId = $this->editCarrierTaxRule($reference, null, $taxRulesGroupReference);
        if ($carrierId) {
            Assert::assertEquals($initialCarrierId, $carrierId->getValue(), 'Carrier ID was expected the remain the same');
        }
    }

    /**
     * @When I set no tax rule for carrier :reference
     */
    public function removeTaxRule(string $reference): void
    {
        $initialCarrierId = $this->getSharedStorage()->get($reference);
        $carrierId = $this->editCarrierTaxRule($reference, null, null);
        if ($carrierId) {
            Assert::assertEquals($initialCarrierId, $carrierId->getValue(), 'Carrier ID was expected the remain the same');
        }
    }

    /**
     * @When I set tax rule :taxRulesGroupReference for carrier :reference I get a new carrier referenced as :newReference
     */
    public function editTaxRuleWithIdUpdate(string $reference, string $newReference, string $taxRulesGroupReference): void
    {
        $initialCarrierId = $this->getSharedStorage()->get($reference);
        $carrierId = $this->editCarrierTaxRule($reference, $newReference, $taxRulesGroupReference);
        if ($carrierId) {
            Assert::assertNotEquals($initialCarrierId, $carrierId->getValue(), 'Carrier ID was expected to be updated');
        }
    }

    protected function editCarrierTaxRule(string $reference, ?string $newReference, ?string $taxRulesGroupReference): ?CarrierId
    {
        $carrierId = $this->referenceToId($reference);

        try {
            if (null === $taxRulesGroupReference) {
                $taxRulesGroupId = 0;
            } else {
                $taxRulesGroupId = 'wrong-tax-rules' === $taxRulesGroupReference ? 4242 : $this->referenceToId($taxRulesGroupReference);
            }
            $command = new SetCarrierTaxRuleGroupCommand(
                $carrierId,
                $taxRulesGroupId,
                ShopConstraint::allShops()
            );

            /** @var CarrierId $carrierIdVO */
            $carrierIdVO = $this->getCommandBus()->handle($command);
            if ($newReference) {
                $this->getSharedStorage()->set($newReference, $carrierIdVO->getValue());
            }
            // Reset cache so that the carrier becomes selectable
            Carrier::resetStaticCache();

            return $carrierIdVO;
        } catch (Exception $e) {
            $this->setLastException($e);
        }

        return null;
    }
}
