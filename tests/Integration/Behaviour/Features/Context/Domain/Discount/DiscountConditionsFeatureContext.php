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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Discount;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountConditionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRule;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroupType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountForEditing;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class DiscountConditionsFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I update discount :discountReference with the condition of a minimum amount:
     *
     * @param string $discountReference
     * @param TableNode $tableNode
     *
     * @return void
     */
    public function updateDiscountConditionMinimalAmount(string $discountReference, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();
        $command = new UpdateDiscountConditionsCommand($this->referenceToId($discountReference));
        $command->setMinimumAmount(
            new DecimalNumber($data['minimum_amount']),
            $this->referenceToId($data['minimum_amount_currency']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included']),
        );
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I update discount :discountReference with the condition it requires at least :quantity products
     *
     * @param string $discountReference
     * @param int $quantity
     *
     * @return void
     */
    public function updateDiscountConditionMinimalProductQuantity(string $discountReference, int $quantity): void
    {
        $command = new UpdateDiscountConditionsCommand($this->referenceToId($discountReference));
        $command->setMinimumProductsQuantity($quantity);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I update discount :discountReference with following conditions matching at least :quantity products:
     *
     * @param string $discountReference
     * @param int $quantity
     * @param TableNode $tableNode
     *
     * @return void
     */
    public function updateDiscountProductConditions(string $discountReference, int $quantity, TableNode $tableNode): void
    {
        $command = new UpdateDiscountConditionsCommand($this->referenceToId($discountReference));

        $conditions = $tableNode->getColumnsHash();

        // This matches the current business rule for the new form, a discount can have multiple criterias that are more and more
        // restrictive, each different rule adds more restriction and the quantity is the same To implement this behaviour we use
        // the new type or ProductRuleGroup, when type is ProductRuleGroupType::ALL_PRODUCT_RULES all the product rules must be valid
        // for the whole product rule group to be valid

        $productRules = [];
        foreach ($conditions as $condition) {
            $productRules[] = new ProductRule(
                ProductRuleType::tryFrom($condition['condition_type']),
                $this->referencesToIds($condition['items'])
            );
        }

        // So far we only handle one product rule group (or one product segment) to match the form behaviour
        $productRulesGroup = [
            new ProductRuleGroup(
                $quantity,
                $productRules,
                ProductRuleGroupType::ALL_PRODUCT_RULES,
            ),
        ];

        // Note: The command is voluntarily loose for now and allows building the groups and rules however we want, depending
        // on future development it may need to evolve into a stricter interface to reflect the business logic more
        $command->setProductConditions($productRulesGroup);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I update discount :discountReference with conditions based on carriers :carrierReferences
     *
     * @param string $discountReference
     * @param string $carrierReferences
     *
     * @return void
     */
    public function updateDiscountCarrierConditions(string $discountReference, string $carrierReferences): void
    {
        $command = new UpdateDiscountConditionsCommand($this->referenceToId($discountReference));
        $command->setCarrierIds($this->referencesToIds($carrierReferences));
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I update discount :discountReference with conditions based on countries :countryReferences
     *
     * @param string $discountReference
     * @param string $countryReferences
     *
     * @return void
     */
    public function updateDiscountCountryConditions(string $discountReference, string $countryReferences): void
    {
        $command = new UpdateDiscountConditionsCommand($this->referenceToId($discountReference));
        $command->setCountryIds($this->referencesToIds($countryReferences));
        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then discount :discountReference should have the following product conditions matching at least :quantity products:
     *
     * @param string $discountReference
     * @param TableNode $tableNode
     *
     * @return void
     */
    public function assertProductConditions(string $discountReference, int $quantity, TableNode $tableNode): void
    {
        /** @var DiscountForEditing $discountForEditing */
        $discountForEditing = $this->getQueryBus()->handle(
            new GetDiscountForEditing($this->getSharedStorage()->get($discountReference))
        );

        $conditionsData = $tableNode->getColumnsHash();
        $productConditions = $discountForEditing->getProductConditions();

        Assert::assertEquals(1, count($productConditions), sprintf('Expected only one product rule group but got %d instead', count($productConditions)));
        $productRuleGroup = $productConditions[0];
        Assert::assertEquals($quantity, $productRuleGroup->getQuantity(), sprintf('Expected at least %d product quantity but got %d instead', $quantity, $productRuleGroup->getQuantity()));
        Assert::assertEquals(ProductRuleGroupType::ALL_PRODUCT_RULES, $productRuleGroup->getType());

        $productRules = $productRuleGroup->getRules();
        Assert::assertEquals(count($conditionsData), count($productRules), sprintf('Expected %d product rules, %d product rules were found', count($conditionsData), count($productRules)));

        foreach ($conditionsData as $index => $conditionData) {
            $productRule = $productRules[$index];
            Assert::assertEquals($conditionData['condition_type'], $productRule->getType()->value);
            $expectedItemIds = $this->referencesToIds($conditionData['items']);
            Assert::assertEquals($expectedItemIds, $productRule->getItemIds(), 'The expected items do not match');
        }
    }

    /**
     * @Then discount :discountReference should have no product conditions
     *
     * @param string $discountReference
     *
     * @return void
     */
    public function assertNoProductConditions(string $discountReference): void
    {
        /** @var DiscountForEditing $discountForEditing */
        $discountForEditing = $this->getQueryBus()->handle(
            new GetDiscountForEditing($this->getSharedStorage()->get($discountReference))
        );

        Assert::assertEmpty($discountForEditing->getProductConditions(), 'Product conditions were found when none is expected');
    }
}
