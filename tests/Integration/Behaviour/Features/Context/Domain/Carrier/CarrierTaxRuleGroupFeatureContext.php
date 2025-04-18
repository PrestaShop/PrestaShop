<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
