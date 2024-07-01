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

use Behat\Gherkin\Node\TableNode;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierTaxRuleGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Domain\TaxRulesGroupFeatureContext;

class CarrierTaxRuleGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I set tax rule for carrier :reference with specified properties:
     */
    public function editTaxRule(string $reference, TableNode $node): void
    {
        $properties = $this->localizeByRows($node);
        $carrierId = $this->referenceToId($reference);

        try {
            if (isset($properties['taxRuleGroup'])) {
                $command = new SetCarrierTaxRuleGroupCommand(
                    $carrierId,
                    (int) TaxRulesGroupFeatureContext::getTaxRulesGroupByName($properties['taxRuleGroup'])->id,
                    ShopConstraint::allShops()
                );

                $newCarrierId = $this->getCommandBus()->handle($command);
                $this->getSharedStorage()->set($reference, $newCarrierId->getValue());
            }
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that tax rules group does not exist
     */
    public function checkCartRuleError(): void
    {
        $this->assertLastErrorIs(
            TaxRulesGroupNotFoundException::class
        );
    }
}
